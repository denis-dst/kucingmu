<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\CatPhoto;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\KtamCard;
use App\Models\MasterWilayah;
use App\Models\MasterBreed;
use App\Models\User;
use App\Services\KtamService;
use App\Services\ImageCompressionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard based on user role.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        } elseif ($user->isDokter()) {
            return $this->dokterDashboard();
        } elseif ($user->isVolunteer()) {
            return $this->volunteerDashboard();
        } else {
            return $this->memberDashboard($request);
        }
    }

    /**
     * Render Admin Dashboard with sorting and status filtering.
     */
    protected function adminDashboard(Request $request)
    {
        $stats = [
            'cats_count' => Cat::count(),
            'cats_alive_count' => Cat::where(function($q) {
                $q->whereNull('status')->orWhereIn('status', ['alive', 'hidup']);
            })->count(),
            'cats_deceased_count' => Cat::whereIn('status', ['deceased', 'mati'])->count(),
            'appointments_count' => Appointment::count(),
            'records_count' => MedicalRecord::count(),
            'ktam_count' => KtamCard::count(),
            'pending_verification_count' => Cat::whereDoesntHave('ktamCard')->count(),
        ];

        $catQuery = Cat::with(['owner', 'ktamCard', 'photos', 'medicalRecords.vet', 'wilayah']);

        // Filter status: all, alive, deceased
        $statusFilter = $request->filled('status') ? strtolower(trim($request->status)) : 'all';
        if ($statusFilter === 'alive') {
            $catQuery->where(function($q) {
                $q->whereNull('status')->orWhereIn('status', ['alive', 'hidup']);
            });
        } elseif ($statusFilter === 'deceased') {
            $catQuery->whereIn('status', ['deceased', 'mati']);
        }

        // Search query
        if ($request->filled('search')) {
            $search = trim($request->search);
            $catQuery->where(function($q) use ($search) {
                $q->where('cats.name', 'like', "%{$search}%")
                  ->orWhere('cats.breed', 'like', "%{$search}%")
                  ->orWhere('cats.unique_code', 'like', "%{$search}%")
                  ->orWhere('cats.color', 'like', "%{$search}%")
                  ->orWhere('cats.gender', 'like', "%{$search}%")
                  ->orWhere('cats.status', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('muhammadiyah_id', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ktamCard', function($kq) use ($search) {
                      $kq->where('ktam_number', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        switch ($sort) {
            case 'name':
                $catQuery->orderBy('cats.name', $direction);
                break;
            case 'owner':
                $catQuery->join('users', 'cats.user_id', '=', 'users.id')
                    ->select('cats.*')
                    ->orderBy('users.name', $direction);
                break;
            case 'breed':
                $catQuery->orderBy('cats.breed', $direction);
                break;
            case 'gender':
                $catQuery->orderBy('cats.gender', $direction);
                break;
            case 'dob':
            case 'date_of_birth':
                $catQuery->orderBy('cats.date_of_birth', $direction);
                break;
            case 'unique_code':
            case 'ktam':
                $catQuery->orderBy('cats.unique_code', $direction);
                break;
            case 'status':
                $catQuery->orderBy('cats.status', $direction);
                break;
            case 'wilayah':
                $catQuery->orderBy('cats.wilayah_code', $direction);
                break;
            case 'created_at':
            default:
                $catQuery->orderBy('cats.created_at', $direction);
                break;
        }

        $cats = $catQuery->paginate(10)->withQueryString();
        $pendingVerificationCats = Cat::whereDoesntHave('ktamCard')
            ->with(['owner', 'photos', 'medicalRecords.vet', 'wilayah'])
            ->latest()
            ->get();
            
        $appointments = Appointment::whereHas('cat')->with(['cat.owner', 'cat.photos'])->orderBy('date', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'cats', 'pendingVerificationCats', 'appointments', 'sort', 'direction', 'statusFilter'));
    }

    /**
     * Render Vet Dashboard.
     */
    protected function dokterDashboard()
    {
        // Queue for today
        $queue = Appointment::whereHas('cat')->with(['cat.owner', 'cat.photos', 'cat.ktamCard'])
            ->whereIn('status', ['scheduled', 'checked_in'])
            ->whereDate('date', Carbon::today())
            ->orderBy('status', 'desc') // checked_in first
            ->orderBy('id', 'asc')
            ->get();

        $recentRecords = MedicalRecord::whereHas('cat')->with(['cat.owner', 'cat.photos', 'appointment'])
            ->where('vet_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('dokter.dashboard', compact('queue', 'recentRecords'));
    }

    /**
     * Render Volunteer Dashboard.
     */
    protected function volunteerDashboard()
    {
        $todayAppointments = Appointment::whereHas('cat')->with(['cat.owner', 'cat.photos', 'cat.ktamCard'])
            ->whereDate('date', Carbon::today())
            ->orderBy('id', 'desc')
            ->get();
        $masterBreeds = MasterBreed::getAllBreedNames();

        return view('volunteer.dashboard', compact('todayAppointments', 'masterBreeds'));
    }

    /**
     * Render Member Dashboard with sorting and status filtering.
     */
    protected function memberDashboard(Request $request = null)
    {
        $request = $request ?: request();
        $catQuery = Auth::user()->cats()->with(['ktamCard', 'medicalRecords.vet', 'photos', 'wilayah']);

        // Filter status: all, alive, deceased
        $statusFilter = $request->filled('status') ? strtolower(trim($request->status)) : 'all';
        if ($statusFilter === 'alive') {
            $catQuery->where(function($q) {
                $q->whereNull('status')->orWhereIn('status', ['alive', 'hidup']);
            });
        } elseif ($statusFilter === 'deceased') {
            $catQuery->whereIn('status', ['deceased', 'mati']);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $catQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('unique_code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        switch ($sort) {
            case 'name':
                $catQuery->orderBy('name', $direction);
                break;
            case 'breed':
                $catQuery->orderBy('breed', $direction);
                break;
            case 'gender':
                $catQuery->orderBy('gender', $direction);
                break;
            case 'dob':
            case 'date_of_birth':
                $catQuery->orderBy('date_of_birth', $direction);
                break;
            case 'status':
                $catQuery->orderBy('status', $direction);
                break;
            case 'created_at':
            default:
                $catQuery->orderBy('created_at', $direction);
                break;
        }

        $cats = $catQuery->get();
        $appointments = Appointment::whereIn('cat_id', Auth::user()->cats()->pluck('id'))
            ->with(['cat.photos'])
            ->latest()
            ->get();

        $activeEvents = \App\Models\Event::where('status', 'active')->orderBy('date', 'asc')->get();
        $masterWilayahs = MasterWilayah::getActiveList();
        $masterBreeds = MasterBreed::getAllBreedNames();

        return view('member.dashboard', compact('cats', 'appointments', 'activeEvents', 'masterWilayahs', 'masterBreeds', 'sort', 'direction', 'statusFilter'));
    }

    /**
     * Store a new cat profile (for Member).
     */
    public function storeCat(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'breed_custom' => 'nullable|string|max:255|required_if:breed,Lainnya',
            'gender' => 'required|in:male,female',
            'status' => 'nullable|in:alive,deceased,hidup,mati',
            'date_of_birth' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'photo_labels.*' => 'nullable|string|max:255',
            'primary_photo_index' => 'nullable|integer',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'biometric_code' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = null;
        $mainPhotoInput = $request->file('photo') ?: $request->input('photo_cam');
        if ($mainPhotoInput) {
            $photoPath = $this->compressAndStorePhoto($mainPhotoInput);
        }

        $biometricPhotoPath = null;
        $biometricInput = $request->file('biometric_photo') ?: $request->input('biometric_photo_cam');
        if ($biometricInput) {
            $biometricPhotoPath = $this->compressAndStorePhoto($biometricInput, 'biometrics');
        }

        $wilayahCode = $request->wilayah_code ?: '34';

        // Process breed and auto-register if custom/new
        $finalBreed = trim($request->breed === 'Lainnya' ? ($request->breed_custom ?: 'Lainnya') : $request->breed);
        MasterBreed::registerBreedIfNotExists($finalBreed);

        $cat = Cat::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'breed' => $finalBreed,
            'gender' => $request->gender,
            'status' => in_array($request->status, ['deceased', 'mati']) ? 'deceased' : 'alive',
            'date_of_birth' => $request->date_of_birth,
            'wilayah_code' => $wilayahCode,
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? 'none',
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        if ($photoPath) {
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        $uploadedPhotos = $request->file('photos', []);
        $cameraPhotos = $request->input('photos_cam', []);
        $allPhotoKeys = array_unique(array_merge(array_keys($uploadedPhotos), array_keys($cameraPhotos)));

        if (!empty($allPhotoKeys)) {
            $labels = $request->input('photo_labels', []);
            $primaryIdx = (int) $request->input('primary_photo_index', -1);

            foreach ($allPhotoKeys as $index) {
                $rawFile = $uploadedPhotos[$index] ?? ($cameraPhotos[$index] ?? null);
                if (!$rawFile) continue;

                $savedPath = $this->compressAndStorePhoto($rawFile);
                $label = isset($labels[$index]) && !empty($labels[$index]) ? $labels[$index] : 'Foto ' . ($index + 1);
                $isPrimary = ($index === $primaryIdx) || (!$photoPath && $index === 0);

                if ($isPrimary && $photoPath) {
                    CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
                    $cat->update(['photo_path' => $savedPath]);
                }

                CatPhoto::create([
                    'cat_id' => $cat->id,
                    'photo_path' => $savedPath,
                    'label' => $label,
                    'is_primary' => $isPrimary,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil dibuat.');
    }

    /**
     * Show the edit cat profile form (for Member).
     */
    public function editCat(Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $cat->load(['photos', 'wilayah']);
        $masterWilayahs = MasterWilayah::getActiveList();
        $masterBreeds = MasterBreed::getAllBreedNames();

        return view('member.edit-cat', compact('cat', 'masterWilayahs', 'masterBreeds'));
    }

    /**
     * Update the cat profile (for Member/Admin).
     */
    public function updateCat(Request $request, Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'breed_custom' => 'nullable|string|max:255|required_if:breed,Lainnya',
            'gender' => 'required|in:male,female',
            'status' => 'nullable|in:alive,deceased,hidup,mati',
            'date_of_birth' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'photo_labels.*' => 'nullable|string|max:255',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'biometric_code' => 'nullable|string|max:255',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $finalBreed = trim($request->breed === 'Lainnya' ? ($request->breed_custom ?: 'Lainnya') : $request->breed);
        MasterBreed::registerBreedIfNotExists($finalBreed);

        $photoPath = $cat->photo_path;
        $mainPhotoInput = $request->file('photo') ?: $request->input('photo_cam');
        if ($mainPhotoInput) {
            if ($cat->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($cat->photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cat->photo_path);
            }
            $photoPath = $this->compressAndStorePhoto($mainPhotoInput);

            CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        $biometricPhotoPath = $cat->biometric_photo_path;
        $biometricInput = $request->file('biometric_photo') ?: $request->input('biometric_photo_cam');
        if ($biometricInput) {
            if ($cat->biometric_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($cat->biometric_photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cat->biometric_photo_path);
            }
            $biometricPhotoPath = $this->compressAndStorePhoto($biometricInput, 'biometrics');
        }

        $oldWilayah = $cat->wilayah_code;
        $newWilayah = $request->wilayah_code ?: ($oldWilayah ?: '34');
        $uniqueCode = $cat->unique_code;

        if (empty($uniqueCode) || $oldWilayah !== $newWilayah) {
            $uniqueCode = Cat::generateUniqueCode($newWilayah, $cat->id);
        }

        $catStatus = $request->filled('status') ? (in_array($request->status, ['deceased', 'mati']) ? 'deceased' : 'alive') : ($cat->status ?: 'alive');

        $cat->update([
            'name' => $request->name,
            'breed' => $finalBreed,
            'gender' => $request->gender,
            'status' => $catStatus,
            'date_of_birth' => $request->date_of_birth,
            'wilayah_code' => $newWilayah,
            'unique_code' => $uniqueCode,
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? $cat->biometric_type,
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code ?? $cat->biometric_code,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        $uploadedPhotos = $request->file('photos', []);
        $cameraPhotos = $request->input('photos_cam', []);
        $allPhotoKeys = array_unique(array_merge(array_keys($uploadedPhotos), array_keys($cameraPhotos)));

        if (!empty($allPhotoKeys)) {
            $labels = $request->input('photo_labels', []);

            foreach ($allPhotoKeys as $key) {
                $rawFile = $uploadedPhotos[$key] ?? ($cameraPhotos[$key] ?? null);
                if (!$rawFile) continue;

                $savedPath = $this->compressAndStorePhoto($rawFile);
                $label = isset($labels[$key]) && !empty($labels[$key]) ? $labels[$key] : 'Foto Kucing';

                $existingForLabel = CatPhoto::where('cat_id', $cat->id)
                    ->whereRaw('LOWER(label) = ?', [strtolower(trim($label))])
                    ->first();

                if ($existingForLabel) {
                    if ($existingForLabel->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingForLabel->photo_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($existingForLabel->photo_path);
                    }
                    $existingForLabel->update([
                        'photo_path' => $savedPath,
                    ]);
                    if ($existingForLabel->is_primary || strtolower(trim($label)) === 'tampak depan') {
                        $cat->update(['photo_path' => $savedPath]);
                    }
                } else {
                    $hasPrimary = CatPhoto::where('cat_id', $cat->id)->where('is_primary', true)->exists();
                    $isPrimary = (strtolower(trim($label)) === 'tampak depan' && !$hasPrimary) || (!$hasPrimary);

                    CatPhoto::create([
                        'cat_id' => $cat->id,
                        'photo_path' => $savedPath,
                        'label' => $label,
                        'is_primary' => $isPrimary,
                    ]);

                    if ($isPrimary) {
                        $cat->update(['photo_path' => $savedPath]);
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil diperbarui.');
    }

    /**
     * Delete (Soft Delete) cat profile (for Member or Admin).
     */
    public function destroyCat(Cat $cat)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ((int) $cat->user_id !== (int) $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $catName = $cat->name;
        $cat->deleted_by = $user->id;
        $cat->save();
        $cat->delete();

        return redirect()->route('dashboard')->with('success', "Data kucing \"{$catName}\" berhasil dihapus.");
    }

    /**
     * Toggle cat life status (alive / deceased).
     */
    public function toggleCatStatus(Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $newStatus = $cat->isAlive() ? 'deceased' : 'alive';
        $cat->update(['status' => $newStatus]);

        $label = $newStatus === 'alive' ? 'Hidup (Aktif)' : 'Mati (Meninggal)';
        return redirect()->back()->with('success', "Status kucing {$cat->name} berhasil diubah menjadi: {$label}.");
    }

    /**
     * Upload an individual photo for cat gallery.
     */
    public function uploadCatPhoto(Request $request, Cat $cat)
    {
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:20480',
            'label' => 'required|string|max:255',
            'is_primary' => 'nullable|boolean',
        ]);

        $photoPath = $this->compressAndStorePhoto($request->file('photo'));
        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
            $cat->update(['photo_path' => $photoPath]);
        } else {
            $hasPrimary = CatPhoto::where('cat_id', $cat->id)->where('is_primary', true)->exists();
            if (!$hasPrimary) {
                $isPrimary = true;
                $cat->update(['photo_path' => $photoPath]);
            }
        }

        CatPhoto::create([
            'cat_id' => $cat->id,
            'photo_path' => $photoPath,
            'label' => $request->label,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->back()->with('success', 'Foto kucing berhasil ditambahkan ke galeri.');
    }

    /**
     * Set a photo as primary for KTAM.
     */
    public function setPrimaryPhoto(CatPhoto $photo)
    {
        $cat = $photo->cat;
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        CatPhoto::where('cat_id', $cat->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        $cat->update(['photo_path' => $photo->photo_path]);

        return redirect()->back()->with('success', 'Foto utama KTAM berhasil diperbarui.');
    }

    /**
     * Delete a photo from cat gallery.
     */
    public function deletePhoto(CatPhoto $photo)
    {
        $cat = $photo->cat;
        if ((int) $cat->user_id !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $wasPrimary = $photo->is_primary;

        if ($photo->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        if ($wasPrimary) {
            $nextPrimary = CatPhoto::where('cat_id', $cat->id)->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
                $cat->update(['photo_path' => $nextPrimary->photo_path]);
            } else {
                $cat->update(['photo_path' => null]);
            }
        }

        return redirect()->back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Book a health checkup appointment (for Member).
     */
    public function storeAppointment(Request $request)
    {
        if (!AppSetting::isEnabled('enable_appointments', true)) {
            return redirect()->back()->with('error', 'Fitur janji temu pemeriksaan sedang dinonaktifkan oleh administrator.');
        }

        $request->validate([
            'cat_id' => 'required|exists:cats,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $cat = Cat::findOrFail($request->cat_id);
        if ((int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        Appointment::create([
            'cat_id' => $request->cat_id,
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Jadwal pemeriksaan berhasil dibuat.');
    }

    /**
     * Store clinical check-up (for Dokter).
     * Dokter only records medical findings. KTAM is issued separately via Admin verification.
     */
    public function storeCheckup(Request $request, Appointment $appointment)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0',
            'temperature' => 'required|numeric|min:0',
            'general_condition' => 'required|string|max:255',
            'deworming_given' => 'nullable|boolean',
            'anti_flea_given' => 'nullable|boolean',
            'supplement_given' => 'nullable|boolean',
            'treatment_notes' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ]);

        MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'cat_id' => $appointment->cat_id,
            'vet_id' => Auth::id(),
            'weight' => $request->weight,
            'temperature' => $request->temperature,
            'general_condition' => $request->general_condition,
            'deworming_given' => $request->has('deworming_given'),
            'anti_flea_given' => $request->has('anti_flea_given'),
            'supplement_given' => $request->has('supplement_given'),
            'treatment_notes' => $request->treatment_notes,
            'recommendation' => $request->recommendation,
        ]);

        $appointment->update(['status' => 'completed']);

        return redirect()->route('dashboard')->with('success', 'Rekam medis berhasil disimpan. Data kucing masuk ke antrian Verifikasi Admin untuk penerbitan KTAM.');
    }

    /**
     * Verify cat data & issue KTAM Card (for Admin).
     */
    public function verifyAndIssueKtam(Request $request, Cat $cat, KtamService $ktamService)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $card = $ktamService->issueCard($cat, Auth::id());

        return redirect()->back()->with('success', 'Kartu KTAM (' . $card->ktam_number . ') berhasil terverifikasi & diterbitkan oleh Admin.');
    }

    /**
     * Download digital KTAM Card as PDF.
     */
    public function downloadKtam(Cat $cat)
    {
        $cat->load(['photos', 'owner', 'wilayah']);
        $card = $cat->ktamCard;
        if (!$card) {
            return redirect()->route('dashboard')->with('error', 'Kucing ini belum memiliki kartu KTAM.');
        }

        if (Auth::user()->role === 'member' && (int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.ktam', compact('card', 'cat'));
        $pdf->setPaper([0, 0, 243.78, 153.07], 'portrait');
        
        return $pdf->stream('ktam_' . str_replace(' ', '_', strtolower($cat->name)) . '.pdf');
    }

    /**
     * Preview Draft KTAM.
     */
    public function previewKtam(Cat $cat)
    {
        $cat->load(['photos', 'owner', 'wilayah']);

        if (Auth::user()->role === 'member' && (int) $cat->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $card = $cat->ktamCard;
        if (!$card) {
            $card = new \stdClass();
            $card->ktam_number = $cat->formatted_unique_code ?? 'DRAFT-XXX-XXX';
            $card->qr_code_payload = asset('images/logo-muhammadiyah.svg'); 
        }

        $isDraft = true;

        return view('pdf.ktam', compact('card', 'cat', 'isDraft'));
    }

    /**
     * Fast check-in (for Volunteer).
     */
    public function checkInAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'checked_in']);
        return redirect()->route('dashboard')->with('success', 'Check-in berhasil. Kucing masuk ke antrian dokter.');
    }

    /**
     * Register a new member & cat at location (for Volunteer/Admin).
     */
    public function quickRegister(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string|max:255',
            'owner_nbm' => 'nullable|string|max:255',
            'cat_name' => 'required|string|max:255',
            'cat_breed' => 'required|string|max:255',
            'cat_breed_custom' => 'nullable|string|max:255|required_if:cat_breed,Lainnya',
            'cat_gender' => 'required|in:male,female',
            'cat_dob' => 'required|date',
            'wilayah_code' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_type' => 'nullable|in:none,paw,nose,both',
            'biometric_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'biometric_code' => 'nullable|string|max:255',
        ]);

        $finalBreed = trim($request->cat_breed === 'Lainnya' ? ($request->cat_breed_custom ?: 'Lainnya') : $request->cat_breed);
        MasterBreed::registerBreedIfNotExists($finalBreed);

        $owner = User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => bcrypt('kucingmu123'),
            'phone' => $request->owner_phone,
            'role' => 'member',
            'muhammadiyah_id' => $request->owner_nbm,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $this->compressAndStorePhoto($file);
        }

        $biometricPhotoPath = null;
        if ($request->hasFile('biometric_photo')) {
            $file = $request->file('biometric_photo');
            $biometricPhotoPath = $this->compressAndStorePhoto($file, 'biometrics');
        }

        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => $request->cat_name,
            'breed' => $finalBreed,
            'gender' => $request->cat_gender,
            'status' => 'alive',
            'date_of_birth' => $request->cat_dob,
            'wilayah_code' => $request->wilayah_code ?: '34',
            'color' => $request->color,
            'photo_path' => $photoPath,
            'biometric_type' => $request->biometric_type ?? 'none',
            'biometric_photo_path' => $biometricPhotoPath,
            'biometric_code' => $request->biometric_code,
        ]);

        if ($photoPath) {
            CatPhoto::create([
                'cat_id' => $cat->id,
                'photo_path' => $photoPath,
                'label' => 'Tampak Depan',
                'is_primary' => true,
            ]);
        }

        Appointment::create([
            'cat_id' => $cat->id,
            'date' => Carbon::today(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in',
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Kucing langsung masuk antrian dokter.');
    }

    /**
     * KTAM Verification Page (Public scan landing).
     */
    public function verifyKtam($number)
    {
        $card = KtamCard::where('ktam_number', $number)
            ->orWhereHas('cat', function($q) use ($number) {
                $q->withTrashed()->where('unique_code', $number);
            })
            ->with('verifier')
            ->first();

        if (!$card) {
            $cat = Cat::withTrashed()->where('unique_code', $number)->firstOrFail();
            $card = $cat->ktamCard ?? new KtamCard([
                'ktam_number' => $cat->formatted_unique_code,
                'issue_date' => $cat->created_at,
            ]);
        } else {
            $cat = $card->cat; // Using withTrashed relation defined on KtamCard
        }

        if ($cat) {
            $cat->loadMissing(['owner', 'photos', 'wilayah', 'deleter']);
        } else if ($card && $card->cat_id) {
            $cat = Cat::withTrashed()->with(['owner', 'photos', 'wilayah', 'deleter'])->find($card->cat_id);
        }

        if (!$cat) {
            abort(404, 'Data Kucing atau KTAM tidak ditemukan.');
        }

        $isDeleted = $cat->trashed();
        $deletedByName = '-';
        $deletedAtFormatted = '-';

        if ($isDeleted) {
            $deleter = $cat->deleter;
            if ($deleter) {
                $roleLabel = match(strtolower($deleter->role ?? '')) {
                    'admin' => 'Admin',
                    'superadmin' => 'Superadmin',
                    'member' => 'Pemilik',
                    default => ucfirst($deleter->role ?? 'Pengguna')
                };
                $deletedByName = $deleter->name . ' (' . $roleLabel . ')';
            } else {
                $deletedByName = 'Administrator / Pemilik';
            }
            $deletedAtFormatted = $cat->deleted_at ? $cat->deleted_at->translatedFormat('d F Y, H:i') . ' WIB' : '-';
        }

        $records = $isDeleted ? collect([]) : MedicalRecord::where('cat_id', $cat->id)->with('vet')->latest()->get();

        return view('ktam-verify', compact('card', 'cat', 'records', 'isDeleted', 'deletedByName', 'deletedAtFormatted'));
    }

    /**
     * Export all data to CSV format (for Admin).
     */
    public function exportData()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=kucingmu_database_export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $cards = KtamCard::whereHas('cat')->with(['cat.owner', 'verifier'])->get();

        $callback = function() use($cards) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['KTAM Number', 'Cat Name', 'Breed', 'Gender', 'Biometric Type', 'Owner Name', 'Owner NBM', 'Owner Phone', 'Issue Date', 'Verified By']);

            foreach ($cards as $card) {
                fputcsv($file, [
                    $card->ktam_number,
                    $card->cat->name,
                    $card->cat->breed,
                    $card->cat->gender,
                    strtoupper($card->cat->biometric_type ?? 'NONE'),
                    $card->cat->owner->name,
                    $card->cat->owner->muhammadiyah_id ?? '-',
                    $card->cat->owner->phone ?? '-',
                    $card->issue_date->format('Y-m-d'),
                    $card->verifier->name ?? 'System/Admin',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Synchronize offline registration and checkup queue (for Volunteer).
     */
    public function syncOffline(Request $request)
    {
        $rawEntries = $request->input('entries', $request->input('items', []));
        $request->merge(['entries' => $rawEntries]);

        $request->validate([
            'entries' => 'required|array',
            'entries.*.owner_name' => 'required|string',
            'entries.*.owner_email' => 'required|email',
            'entries.*.owner_phone' => 'required|string',
            'entries.*.owner_nbm' => 'nullable|string',
            'entries.*.cat_name' => 'required|string',
            'entries.*.cat_breed' => 'required|string',
            'entries.*.cat_gender' => 'required|in:male,female',
            'entries.*.cat_dob' => 'required|date',
        ]);

        $syncedCount = 0;
        foreach ($request->entries as $entry) {
            $owner = User::where('email', $entry['owner_email'])->first();
            if (!$owner) {
                $owner = User::create([
                    'name' => $entry['owner_name'],
                    'email' => $entry['owner_email'],
                    'password' => bcrypt('kucingmu123'),
                    'phone' => $entry['owner_phone'],
                    'role' => 'member',
                    'muhammadiyah_id' => $entry['owner_nbm'],
                ]);
            }

            $rawBreed = trim($entry['cat_breed'] ?? 'Domestik');
            MasterBreed::registerBreedIfNotExists($rawBreed);

            $cat = Cat::create([
                'user_id' => $owner->id,
                'name' => $entry['cat_name'],
                'breed' => $rawBreed,
                'gender' => $entry['cat_gender'],
                'date_of_birth' => $entry['cat_dob'],
            ]);

            Appointment::create([
                'cat_id' => $cat->id,
                'date' => Carbon::today(),
                'time_slot' => 'Offline Field Mode',
                'status' => 'checked_in',
                'notes' => 'Disinkronkan dari antrian offline lapangan.',
            ]);

            $syncedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => $syncedCount . ' data antrian offline berhasil disinkronkan.',
        ]);
    }

    /**
     * Compress and store an uploaded photo using ImageCompressionService (guaranteed <= 200KB).
     *
     * @param mixed $file \Illuminate\Http\UploadedFile|string
     * @param string $dir
     * @return string The relative storage path (e.g. 'cats/xxxxx.jpg')
     */
    private function compressAndStorePhoto($file, string $dir = 'cats'): string
    {
        $savedPath = ImageCompressionService::compressAndStore($file, $dir, 'public', 200);

        if (!$savedPath) {
            throw new \RuntimeException('Gagal mengompres dan menyimpan file foto.');
        }

        return $savedPath;
    }
}
