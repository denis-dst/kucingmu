"""
Cat Face Recognition Pipeline — KucingMu / PTMA
================================================
Stack  : YOLOv8 (ultralytics) · ResNet50 (torchvision) · PyTorch
DB     : MySQL via pymysql — mirrors Laravel tables
            ptma_cat_censuses   → id_kucing, foto_wajah_embedding
            cats                → name / breed (KTAM member cats)
            stray_cat_surveys   → (photo-only, no stored embedding)

Usage:
    python cat_face_recognition.py          # interactive demo
    from cat_face_recognition import CatFaceRecognizer
"""

from __future__ import annotations

import json
import os
import warnings
from dataclasses import dataclass, field
from pathlib import Path
from typing import Optional

import numpy as np
import pymysql
import torch
import torchvision.transforms as T
from PIL import Image
from torchvision.models import ResNet50_Weights, resnet50
from ultralytics import YOLO

warnings.filterwarnings("ignore", category=FutureWarning)

# ─────────────────────────────────────────────
# Config
# ─────────────────────────────────────────────

DB_CONFIG: dict = {
    "host": "127.0.0.1",
    "port": 3306,
    "user": "root",
    "password": "",
    "database": "kucingmu",
    "charset": "utf8mb4",
}

STORAGE_ROOT = Path(__file__).parent.parent.parent / "storage" / "app" / "public"

EMBED_DIM = 512          # size of the L2-normalised face vector
COSINE_THRESHOLD = 0.72  # >= this -> "known cat"  (matches controller's 72%)
DETECT_CONF = 0.30       # YOLO min-confidence for cat face box


# ─────────────────────────────────────────────
# Data classes
# ─────────────────────────────────────────────

@dataclass
class RecognitionResult:
    id_kucing: str
    source_type: str          # "census" | "member_cat"
    similarity: float
    is_match: bool
    extra: dict = field(default_factory=dict)


# ─────────────────────────────────────────────
# 1. Face Detection — YOLOv8
# ─────────────────────────────────────────────

class CatFaceDetector:
    """
    Wraps YOLOv8 to locate cat faces in an image.

    The standard 'yolov8n.pt' detects generic animals (class 15 = cat).
    For production, swap to a custom model fine-tuned on cat faces so the
    bounding box is tight around the face rather than the whole body.
    """

    # COCO class index for 'cat'
    _CAT_CLASS = 15

    def __init__(self, model_path: str = "yolov8n.pt") -> None:
        self.model = YOLO(model_path)

    def detect(self, image: Image.Image) -> list[Image.Image]:
        """
        Return cropped face patches sorted by confidence (highest first).
        Falls back to the full image when no cat is detected — ensures the
        pipeline always produces an embedding even for close-up photos.
        """
        results = self.model(image, verbose=False)[0]
        crops: list[tuple[float, Image.Image]] = []

        for box in results.boxes:
            cls = int(box.cls[0].item())
            conf = float(box.conf[0].item())

            if cls != self._CAT_CLASS or conf < DETECT_CONF:
                continue

            x1, y1, x2, y2 = (int(v) for v in box.xyxy[0].tolist())
            # Add 15% padding so ears and forehead stay in frame
            pad_x = int((x2 - x1) * 0.15)
            pad_y = int((y2 - y1) * 0.15)
            w, h = image.size
            crop = image.crop((
                max(0, x1 - pad_x), max(0, y1 - pad_y),
                min(w, x2 + pad_x), min(h, y2 + pad_y),
            ))
            crops.append((conf, crop))

        if not crops:
            return [image]  # no detection -> use full image

        crops.sort(key=lambda t: t[0], reverse=True)
        return [c for _, c in crops]


# ─────────────────────────────────────────────
# 2. Feature Extraction — ResNet50 Embedding
# ─────────────────────────────────────────────

class CatEmbedder:
    """
    ResNet50 with the final FC layer replaced by a 512-d projection head.
    Outputs L2-normalised vectors — required for cosine similarity to be
    equivalent to dot product (makes the math in PHP identical).
    """

    _TRANSFORM = T.Compose([
        T.Resize(256),
        T.CenterCrop(224),
        T.ToTensor(),
        T.Normalize(mean=[0.485, 0.456, 0.406],
                    std=[0.229, 0.224, 0.225]),
    ])

    def __init__(self, weights_path: Optional[str] = None) -> None:
        backbone = resnet50(weights=ResNet50_Weights.IMAGENET1K_V2)

        # Replace classifier: avgpool -> Linear(2048, 512)
        backbone.fc = torch.nn.Sequential(
            torch.nn.Linear(backbone.fc.in_features, EMBED_DIM),
            torch.nn.BatchNorm1d(EMBED_DIM),
        )

        if weights_path and Path(weights_path).exists():
            state = torch.load(weights_path, map_location="cpu")
            backbone.load_state_dict(state)

        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        self.model = backbone.to(self.device).eval()

    @torch.no_grad()
    def embed(self, image: Image.Image) -> list[float]:
        """
        Return a 512-d L2-normalised embedding as a plain Python list.
        The list format matches the JSON stored in foto_wajah_embedding.
        """
        tensor = self._TRANSFORM(image.convert("RGB")).unsqueeze(0).to(self.device)
        vec = self.model(tensor).squeeze(0)
        # L2 normalise so cosine similarity == dot product
        vec = torch.nn.functional.normalize(vec, p=2, dim=0)
        return vec.cpu().tolist()


# ─────────────────────────────────────────────
# 3. Similarity — Cosine + optional Euclidean
# ─────────────────────────────────────────────

def cosine_similarity(a: list[float], b: list[float]) -> float:
    """Mirrors PtmaCatCensus::cosineSimilarity() in PHP."""
    va = np.array(a, dtype=np.float32)
    vb = np.array(b, dtype=np.float32)
    norm = np.linalg.norm(va) * np.linalg.norm(vb)
    if norm < 1e-8:
        return 0.0
    return float(np.clip(np.dot(va, vb) / norm, 0.0, 1.0))


def euclidean_distance(a: list[float], b: list[float]) -> float:
    """Smaller = more similar. Useful as a secondary filter."""
    return float(np.linalg.norm(np.array(a) - np.array(b)))


# ─────────────────────────────────────────────
# 4. Database helpers
# ─────────────────────────────────────────────

def _db_connect() -> pymysql.Connection:
    return pymysql.connect(**DB_CONFIG, cursorclass=pymysql.cursors.DictCursor)


def _load_embedding(raw) -> Optional[list[float]]:
    """Parse JSON embedding stored as TEXT in MySQL."""
    if raw is None:
        return None
    if isinstance(raw, (list, tuple)):
        return list(raw)
    try:
        return json.loads(raw)
    except (json.JSONDecodeError, TypeError):
        return None


# ─────────────────────────────────────────────
# 5. Main recognizer
# ─────────────────────────────────────────────

class CatFaceRecognizer:
    """
    End-to-end pipeline: image -> face crop -> embedding -> DB match.

    register_cat()  : store a new cat's embedding in ptma_cat_censuses
    recognize_cat() : find the closest match across all registered cats
    """

    def __init__(
        self,
        yolo_model: str = "yolov8n.pt",
        resnet_weights: Optional[str] = None,
        threshold: float = COSINE_THRESHOLD,
    ) -> None:
        print("Loading detector ...")
        self.detector = CatFaceDetector(yolo_model)
        print("Loading embedder ...")
        self.embedder = CatEmbedder(resnet_weights)
        self.threshold = threshold

    # -- internal helpers --

    def _image_to_embedding(self, image_path: str) -> list[float]:
        """Detect face -> crop -> embed. Returns a single 512-d vector."""
        img = Image.open(image_path).convert("RGB")
        faces = self.detector.detect(img)
        # Use the highest-confidence crop (first element after sorting)
        return self.embedder.embed(faces[0])

    # -- public API --

    def register_cat(self, image_path: str, id_kucing: str) -> bool:
        """
        Compute an embedding for image_path and persist it to the
        foto_wajah_embedding column of the matching ptma_cat_censuses row.

        id_kucing must already exist in the table (created via the Laravel form).
        Returns True on success.
        """
        embedding = self._image_to_embedding(image_path)
        embedding_json = json.dumps(embedding)

        conn = _db_connect()
        try:
            with conn.cursor() as cur:
                affected = cur.execute(
                    "UPDATE ptma_cat_censuses "
                    "SET foto_wajah_embedding = %s "
                    "WHERE id_kucing = %s",
                    (embedding_json, id_kucing),
                )
            conn.commit()
            if affected == 0:
                raise ValueError(f"id_kucing '{id_kucing}' not found in ptma_cat_censuses")
            print(f"[register] {id_kucing} -> embedding saved ({EMBED_DIM}d)")
            return True
        finally:
            conn.close()

    def recognize_cat(
        self,
        image_path: str,
        kampus: Optional[str] = None,
    ) -> Optional[RecognitionResult]:
        """
        Extract an embedding from image_path and compare it against every row in
        ptma_cat_censuses that has a stored foto_wajah_embedding.

        kampus -- optional filter (e.g. "UMY") to narrow the search, identical to
                  the kampus parameter accepted by PtmaCatCensusController::match().

        Returns the best RecognitionResult, or None when no candidate clears
        COSINE_THRESHOLD.
        """
        query_vec = self._image_to_embedding(image_path)

        conn = _db_connect()
        try:
            with conn.cursor() as cur:
                sql = (
                    "SELECT id_kucing, kampus, kampus_custom, zona, usia, gender, "
                    "warna, foto_wajah_embedding "
                    "FROM ptma_cat_censuses "
                    "WHERE foto_wajah_embedding IS NOT NULL"
                )
                params: list = []
                if kampus and kampus != "Semua":
                    sql += " AND kampus = %s"
                    params.append(kampus)
                cur.execute(sql, params)
                rows = cur.fetchall()
        finally:
            conn.close()

        best_sim = -1.0
        best_row: Optional[dict] = None

        for row in rows:
            stored_vec = _load_embedding(row["foto_wajah_embedding"])
            if stored_vec is None:
                continue
            sim = cosine_similarity(query_vec, stored_vec)
            if sim > best_sim:
                best_sim = sim
                best_row = row

        if best_row is None or best_sim < self.threshold:
            print(
                f"[recognize] No match — best sim={best_sim:.4f} "
                f"(threshold={self.threshold})"
            )
            return None

        result = RecognitionResult(
            id_kucing=best_row["id_kucing"],
            source_type="census",
            similarity=round(best_sim, 4),
            is_match=True,
            extra={
                "kampus": best_row.get("kampus"),
                "zona": best_row.get("zona"),
                "usia": best_row.get("usia"),
                "gender": best_row.get("gender"),
                "warna": best_row.get("warna"),
                "similarity_percent": round(best_sim * 100, 1),
            },
        )
        print(
            f"[recognize] {result.id_kucing} — "
            f"{result.extra['similarity_percent']}% match"
        )
        return result

    def recognize_all_sources(
        self,
        image_path: str,
        top_k: int = 6,
    ) -> list[RecognitionResult]:
        """
        Search across ptma_cat_censuses AND cats (KTAM member cats).
        Returns up to top_k results sorted by similarity, mirroring the
        multi-source logic in PtmaCatCensusController::match().
        """
        query_vec = self._image_to_embedding(image_path)
        candidates: list[RecognitionResult] = []

        conn = _db_connect()
        try:
            # Source 1 — PTMA census
            with conn.cursor() as cur:
                cur.execute(
                    "SELECT id_kucing, kampus, zona, usia, gender, warna, "
                    "foto_wajah_embedding "
                    "FROM ptma_cat_censuses "
                    "WHERE foto_wajah_embedding IS NOT NULL"
                )
                for row in cur.fetchall():
                    vec = _load_embedding(row["foto_wajah_embedding"])
                    if vec is None:
                        continue
                    sim = cosine_similarity(query_vec, vec)
                    if sim >= self.threshold:
                        candidates.append(RecognitionResult(
                            id_kucing=row["id_kucing"],
                            source_type="census",
                            similarity=round(sim, 4),
                            is_match=True,
                            extra={"kampus": row.get("kampus"), "zona": row.get("zona")},
                        ))

            # Source 2 — KTAM member cats (no stored embedding; skip)
            # Embeddings for `cats` are not persisted yet in this schema.
            # To enable: add a face_embedding TEXT column to `cats`, then
            # populate via register_member_cat() and query it here.

        finally:
            conn.close()

        candidates.sort(key=lambda r: r.similarity, reverse=True)
        return candidates[:top_k]


# ─────────────────────────────────────────────
# 6. Batch embedding — backfill existing photos
# ─────────────────────────────────────────────

def backfill_embeddings(
    recognizer: CatFaceRecognizer,
    limit: int = 20,
) -> int:
    """
    Compute and store embeddings for census rows that have foto_wajah but
    no foto_wajah_embedding yet — mirrors getMissingEmbeddings() + syncEmbeddings().
    Returns the number of rows updated.
    """
    conn = _db_connect()
    updated = 0
    try:
        with conn.cursor() as cur:
            cur.execute(
                "SELECT id, id_kucing, foto_wajah "
                "FROM ptma_cat_censuses "
                "WHERE foto_wajah IS NOT NULL "
                "  AND foto_wajah_embedding IS NULL "
                "LIMIT %s",
                (limit,),
            )
            rows = cur.fetchall()

        for row in rows:
            photo_path = STORAGE_ROOT / row["foto_wajah"]
            if not photo_path.exists():
                print(f"  [skip] {row['id_kucing']} — file not found: {photo_path}")
                continue

            try:
                embedding = recognizer._image_to_embedding(str(photo_path))
                with conn.cursor() as cur:
                    cur.execute(
                        "UPDATE ptma_cat_censuses "
                        "SET foto_wajah_embedding = %s WHERE id = %s",
                        (json.dumps(embedding), row["id"]),
                    )
                conn.commit()
                updated += 1
                print(f"  [ok] {row['id_kucing']}")
            except Exception as exc:
                print(f"  [err] {row['id_kucing']}: {exc}")

    finally:
        conn.close()

    return updated


# ─────────────────────────────────────────────
# 7. Interactive demo
# ─────────────────────────────────────────────

if __name__ == "__main__":
    import sys

    recognizer = CatFaceRecognizer()

    if len(sys.argv) >= 3 and sys.argv[1] == "register":
        image_path = sys.argv[2]
        id_kucing = sys.argv[3] if len(sys.argv) > 3 else input("id_kucing: ")
        recognizer.register_cat(image_path, id_kucing)

    elif len(sys.argv) == 3 and sys.argv[1] == "recognize":
        result = recognizer.recognize_all_sources(sys.argv[2])
        if result:
            print("\nTop matches:")
            for r in result:
                print(f"  {r.id_kucing:12s}  {r.extra.get('kampus','?'):6s}  "
                      f"sim={r.similarity:.4f}  ({r.similarity*100:.1f}%)")
        else:
            print("No match found above threshold.")

    elif len(sys.argv) == 2 and sys.argv[1] == "backfill":
        n = backfill_embeddings(recognizer)
        print(f"Backfill complete — {n} rows updated.")

    else:
        print(__doc__)
        print("Commands:")
        print("  python cat_face_recognition.py register <img> <id_kucing>")
        print("  python cat_face_recognition.py recognize <img>")
        print("  python cat_face_recognition.py backfill")
