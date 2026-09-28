<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Cat;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordSubjective;
use App\Models\MedicalRecordObjective;
use App\Models\MedicalRecordDiagnosis;
use App\Models\MedicalRecordPlan;
use App\Models\MedicalRecordAdvice;
use App\Models\MedicalRecordFollowup;
use App\Models\MedicalRecordAuditLog;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VetMedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records, today's examination queue, and drafts.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Today's Appointment Queue
        $queue = Appointment::whereHas('cat')
            ->with(['cat.owner', 'cat.photos', 'cat.ktamCard'])
            ->whereIn('status', ['scheduled', 'checked_in'])
            ->whereDate('date', Carbon::today())
            ->orderBy('status', 'desc') // checked_in first
            ->orderBy('id', 'asc')
            ->get();

        // 2. Query Medical Records with filters
        $query = MedicalRecord::with(['cat.owner', 'cat.photos', 'doctor', 'primaryDiagnosis', 'prescriptions'])
            ->latest('created_at');

        // Non-admin doctors only see their assigned or created records by default, unless searching
        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('vet_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Service type filter
        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }

        // Search query (cat name, owner name, record number, chief complaint)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('record_number', 'like', "%{$search}%")
                  ->orWhere('chief_complaint', 'like', "%{$search}%")
                  ->orWhereHas('cat', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('breed', 'like', "%{$search}%")
                         ->orWhere('unique_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('muhammadiyah_id', 'like', "%{$search}%");
                  });
            });
        }

        $records = $query->paginate(12)->withQueryString();

        // Statistics
        $stats = [
            'today_queue' => $queue->count(),
            'in_progress' => MedicalRecord::where('vet_id', $user->id)->whereIn('status', ['draft', 'in_progress'])->count(),
            'completed' => MedicalRecord::where('vet_id', $user->id)->where('status', 'completed')->count(),
            'total_issued_rx' => Prescription::where('prescribed_by', $user->id)->where('status', 'issued')->count(),
        ];

        return view('dokter.medical-records.index', compact('queue', 'records', 'stats'));
    }

    /**
     * Show form to initiate a new medical record examination.
     */
    public function create(Request $request)
    {
        $selectedCatId = $request->input('cat_id');
        $selectedAppointmentId = $request->input('appointment_id');
        $selectedCat = null;
        $appointment = null;

        if ($selectedAppointmentId) {
            $appointment = Appointment::with(['cat.owner', 'cat.photos', 'cat.ktamCard'])->find($selectedAppointmentId);
            if ($appointment) {
                $selectedCat = $appointment->cat;
            }
        } elseif ($selectedCatId) {
            $selectedCat = Cat::with(['owner', 'photos', 'ktamCard'])->find($selectedCatId);
        }

        // Only show cats that have been verified and have an official KTAKuMu
        $availableCats = Cat::with(['owner', 'ktamCard'])
            ->verifiedWithKtakumu()
            ->orderBy('name', 'asc')
            ->get();

        return view('dokter.medical-records.create', compact('selectedCat', 'appointment', 'availableCats'));
    }

    /**
     * Store new medical record header and initialize SOAP workspace.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cat_id' => 'required|exists:cats,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'service_type' => 'required|in:clinic,online',
            'chief_complaint' => 'required|string|max:1000',
            'clinic_name' => 'nullable|string|max:150',
        ]);

        $cat = Cat::findOrFail($request->cat_id);

        if (!$cat->hasKtakumu()) {
            return back()->withInput()->withErrors([
                'cat_id' => 'Kucing belum diverifikasi atau belum memiliki KTAKuMu resmi.',
            ]);
        }

        // Check if an open draft already exists for this appointment
        if ($request->appointment_id) {
            $existing = MedicalRecord::where('appointment_id', $request->appointment_id)
                ->whereIn('status', ['draft', 'in_progress'])
                ->first();

            if ($existing) {
                return redirect()->route('dokter.medical-records.examine', $existing)
                    ->with('info', 'Membuka draft pemeriksaan yang sudah ada untuk antrian ini.');
            }
        }

        DB::beginTransaction();
        try {
            $recordNumber = MedicalRecord::generateRecordNumber();

            $record = MedicalRecord::create([
                'record_number' => $recordNumber,
                'appointment_id' => $request->appointment_id,
                'cat_id' => $cat->id,
                'member_id' => $cat->user_id,
                'vet_id' => Auth::id(),
                'service_type' => $request->service_type,
                'clinic_name' => $request->clinic_name ?: 'Klinik Hewan KucingMu',
                'status' => MedicalRecord::STATUS_IN_PROGRESS,
                'chief_complaint' => $request->chief_complaint,
                'weight' => 0.00,
                'temperature' => 0.0,
                'general_condition' => 'Dalam Pemeriksaan',
                'started_at' => now(),
                'created_by' => Auth::id(),
            ]);

            // Initialize Subjective with member complaint
            MedicalRecordSubjective::create([
                'medical_record_id' => $record->id,
                'member_complaint' => $request->chief_complaint,
                'symptom_onset' => $request->input('symptom_onset', 'Baru-baru ini'),
                'appetite_history' => 'Normal',
                'drinking_history' => 'Normal',
                'urination_history' => 'Normal',
                'defecation_history' => 'Normal',
            ]);

            // Create default draft prescription header
            Prescription::create([
                'prescription_number' => Prescription::generatePrescriptionNumber(),
                'medical_record_id' => $record->id,
                'prescribed_by' => Auth::id(),
                'status' => 'draft',
            ]);

            // Log Audit
            MedicalRecordAuditLog::log($record, Auth::user(), 'created', 'Pemeriksaan SOAP baru dimulai oleh dokter hewan.');

            DB::commit();

            return redirect()->route('dokter.medical-records.examine', $record)
                ->with('success', "Pemeriksaan #{$record->record_number} berhasil dimulai.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal membuat rekam medis baru: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memulai pemeriksaan: ' . $e->getMessage());
        }
    }

    /**
     * Stepper workspace for conducting the 5-step SOAP veterinary examination.
     */
    public function stepper(MedicalRecord $record, Request $request)
    {
        $this->authorizeAccess($record);

        $record->load([
            'cat.owner',
            'cat.photos',
            'cat.ktamCard',
            'appointment',
            'subjective',
            'objectives',
            'diagnoses',
            'plans',
            'prescriptions.items.medicine',
            'advices',
            'followups',
            'auditLogs.actor',
        ]);

        // Previous medical records for patient medical history
        $previousRecords = MedicalRecord::where('cat_id', $record->cat_id)
            ->where('id', '!=', $record->id)
            ->where('status', 'completed')
            ->with(['doctor', 'primaryDiagnosis', 'prescriptions.items'])
            ->latest('completed_at')
            ->take(5)
            ->get();

        // Active master medicines for autocomplete
        $medicines = Medicine::active()->orderBy('name', 'asc')->get();

        $activeStep = $request->query('step', 1);

        return view('dokter.medical-records.examine', compact('record', 'previousRecords', 'medicines', 'activeStep'));
    }

    /**
     * Save a specific step of the SOAP examination (AJAX or normal submit).
     */
    public function saveStep(Request $request, MedicalRecord $record, string $step)
    {
        $this->authorizeEdit($record);

        DB::beginTransaction();
        try {
            switch ($step) {
                case 'subjective':
                    $this->saveSubjectiveStep($request, $record);
                    break;

                case 'objective':
                    $this->saveObjectiveStep($request, $record);
                    break;

                case 'assessment':
                    $this->saveAssessmentStep($request, $record);
                    break;

                case 'plan':
                    $this->savePlanStep($request, $record);
                    break;

                default:
                    throw new \InvalidArgumentException("Langkah SOAP '{$step}' tidak dikenali.");
            }

            MedicalRecordAuditLog::log(
                $record,
                Auth::user(),
                'updated_draft',
                "Dokter memperbarui catatan step '{$step}'."
            );

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Data {$step} berhasil disimpan sebagai draft.",
                    'record' => $record->fresh(),
                ]);
            }

            $nextStep = match ($step) {
                'subjective' => 2,
                'objective' => 3,
                'assessment' => 4,
                'plan' => 5,
                default => 5,
            };

            return redirect()->route('dokter.medical-records.examine', ['record' => $record->id, 'step' => $nextStep])
                ->with('success', "Data step " . ucfirst($step) . " tersimpan. Melanjutkan ke langkah {$nextStep}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan step {$step}: " . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan step: ' . $e->getMessage());
        }
    }

    /**
     * Add structured prescription item to draft prescription.
     */
    public function addPrescriptionItem(Request $request, MedicalRecord $record)
    {
        $this->authorizeEdit($record);

        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'medicine_id' => 'nullable|exists:medicines,id',
            'dosage_form' => 'required|string|max:50',
            'dose_value' => 'required|numeric|min:0.01',
            'dose_unit' => 'required|string|max:30',
            'dose_basis' => 'required|in:fixed,per_kg,other',
            'administration_route' => 'required|string|max:50',
            'frequency_value' => 'required|string|max:50',
            'duration_value' => 'required|string|max:50',
            'quantity_value' => 'nullable|numeric|min:0.1',
            'quantity_unit' => 'nullable|string|max:30',
            'usage_instructions' => 'required|string|max:500',
            'warnings' => 'nullable|string|max:500',
        ]);

        $prescription = Prescription::firstOrCreate(
            ['medical_record_id' => $record->id, 'status' => 'draft'],
            [
                'prescription_number' => Prescription::generatePrescriptionNumber(),
                'prescribed_by' => Auth::id(),
            ]
        );

        $medicine = $request->medicine_id ? Medicine::find($request->medicine_id) : null;

        $item = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine ? $medicine->id : null,
            'medicine_name_snapshot' => $request->medicine_name,
            'active_ingredient' => $medicine ? $medicine->active_ingredient : $request->input('active_ingredient'),
            'dosage_form' => $request->dosage_form,
            'concentration_value' => $medicine ? $medicine->concentration_value : $request->input('concentration_value'),
            'concentration_unit' => $medicine ? $medicine->concentration_unit : $request->input('concentration_unit'),
            'dose_value' => $request->dose_value,
            'dose_unit' => $request->dose_unit,
            'dose_basis' => $request->dose_basis,
            'administration_route' => $request->administration_route,
            'frequency_value' => $request->frequency_value,
            'frequency_unit' => $request->input('frequency_unit', 'kali sehari'),
            'duration_value' => $request->duration_value,
            'duration_unit' => $request->input('duration_unit', 'hari'),
            'quantity_value' => $request->quantity_value ?: 1,
            'quantity_unit' => $request->input('quantity_unit', $request->dosage_form),
            'usage_instructions' => $request->usage_instructions,
            'warnings' => $request->warnings,
        ]);

        MedicalRecordAuditLog::log($record, Auth::user(), 'prescription_item_added', "Menambahkan resep: {$item->medicine_name_snapshot}.");

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Obat {$item->medicine_name_snapshot} berhasil ditambahkan ke resep.",
                'item' => $item,
            ]);
        }

        return redirect()->back()->with('success', "Obat {$item->medicine_name_snapshot} berhasil ditambahkan ke resep.");
    }

    /**
     * Delete prescription item from draft.
     */
    public function deletePrescriptionItem(PrescriptionItem $item)
    {
        $prescription = $item->prescription;
        $record = $prescription->medicalRecord;

        $this->authorizeEdit($record);

        $name = $item->medicine_name_snapshot;
        $item->delete();

        MedicalRecordAuditLog::log($record, Auth::user(), 'prescription_item_deleted', "Menghapus obat '{$name}' dari draft resep.");

        return redirect()->back()->with('success', "Obat '{$name}' berhasil dihapus dari resep.");
    }

    /**
     * Finalize the medical record (Step 5). Locks record from further direct edits.
     */
    public function finalize(Request $request, MedicalRecord $record)
    {
        $this->authorizeEdit($record);

        // Validation against clinical requirements in PRD Section 10.2
        if (!$record->diagnoses()->where('is_primary', true)->exists() && !$record->diagnoses()->exists()) {
            return redirect()->back()->with('error', 'Validasi Gagal: Rekam medis wajib memiliki minimal satu diagnosis (Assessment) sebelum difinalisasi.');
        }

        if ((float) $record->weight <= 0) {
            return redirect()->back()->with('error', 'Validasi Gagal: Berat badan pasien (kg) wajib diukur dan dicatat pada Step Objective.');
        }

        DB::beginTransaction();
        try {
            $record->update([
                'status' => MedicalRecord::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // Finalize draft prescriptions into 'issued'
            $prescriptions = $record->prescriptions()->where('status', 'draft')->get();
            foreach ($prescriptions as $rx) {
                if ($rx->items()->exists()) {
                    $rx->update([
                        'status' => 'issued',
                        'issued_at' => now(),
                    ]);
                } else {
                    // Empty draft prescription can be removed
                    $rx->delete();
                }
            }

            // If attached to appointment, mark appointment completed
            if ($record->appointment) {
                $record->appointment->update(['status' => 'completed']);
            }

            // Log finalization audit
            MedicalRecordAuditLog::log(
                $record,
                Auth::user(),
                'finalized',
                'Rekam Medis resmi difinalisasi dan diterbitkan oleh dokter hewan.'
            );

            DB::commit();

            return redirect()->route('dokter.medical-records.show', $record)
                ->with('success', "Rekam Medis #{$record->record_number} resmi difinalisasi! Ringkasan dan resep telah diterbitkan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal finalisasi rekam medis: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memfinalisasi rekam medis: ' . $e->getMessage());
        }
    }

    /**
     * Show finalized clinical sheet & review.
     */
    public function show(MedicalRecord $record)
    {
        $this->authorizeAccess($record);

        $record->load([
            'cat.owner',
            'cat.photos',
            'cat.ktamCard',
            'doctor',
            'appointment',
            'subjective',
            'objectives',
            'diagnoses',
            'plans',
            'prescriptions.items.medicine',
            'advices',
            'followups',
            'auditLogs.actor',
        ]);

        return view('dokter.medical-records.show', compact('record'));
    }

    /**
     * Make an amendment to a finalized medical record with mandatory reason (PRD 10.3).
     */
    public function amend(Request $request, MedicalRecord $record)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$record->isCompleted()) {
            return redirect()->back()->with('error', 'Amendemen hanya dapat dibuat untuk rekam medis yang telah difinalisasi.');
        }

        if (!$user->isAdmin() && (int) $record->vet_id !== (int) $user->id) {
            abort(403, 'Anda tidak berwenang mengamendemen rekam medis dokter lain.');
        }

        $request->validate([
            'amendment_reason' => 'required|string|min:10|max:1000',
            'amended_notes' => 'required|string',
            'internal_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldNotes = $record->treatment_notes;

            $record->update([
                'treatment_notes' => $request->amended_notes,
                'internal_notes' => $request->internal_notes ?: $record->internal_notes,
            ]);

            MedicalRecordAuditLog::log(
                $record,
                $user,
                'amended',
                $request->amendment_reason,
                [
                    'previous_treatment_notes' => $oldNotes,
                    'new_treatment_notes' => $request->amended_notes,
                ]
            );

            DB::commit();

            return redirect()->route('dokter.medical-records.show', $record)
                ->with('success', 'Amendemen rekam medis berhasil dicatat ke dalam audit trail.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal mencatat amendemen: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencatat amendemen: ' . $e->getMessage());
        }
    }

    /**
     * Render clean printable clinical sheet for official veterinary documentation.
     */
    public function printSheet(MedicalRecord $record)
    {
        $this->authorizeAccess($record);

        $record->load([
            'cat.owner',
            'cat.ktamCard',
            'doctor',
            'subjective',
            'objectives',
            'diagnoses',
            'plans',
            'prescriptions.items',
            'advices',
            'followups',
        ]);

        return view('dokter.medical-records.print', compact('record'));
    }

    /**
     * Show member-facing medical summary (PRD Section 9).
     * Internal notes and audit logs are strictly excluded.
     */
    public function memberSummary(MedicalRecord $record)
    {
        /** @var User $user */
        $user = Auth::user();

        // Security check: Only the cat owner or clinic staff can view
        $isOwner = (int) $record->cat->user_id === (int) $user->id || (int) $record->member_id === (int) $user->id;
        if (!$isOwner && !$user->isAdmin() && !$user->hasRole('dokter')) {
            abort(403, 'Anda tidak memiliki hak akses untuk ringkasan medis kucing ini.');
        }

        if (!$record->isCompleted() && !$user->hasRole('dokter') && !$user->isAdmin()) {
            return redirect()->back()->with('error', 'Rekam medis ini masih dalam proses pemeriksaan oleh dokter.');
        }

        $record->load([
            'cat.owner',
            'cat.photos',
            'cat.ktamCard',
            'doctor',
            'primaryDiagnosis',
            'prescriptions' => function ($q) {
                $q->where('status', 'issued')->with('items.medicine');
            },
            'advices' => function ($q) {
                $q->where('visible_to_member', true);
            },
            'followups',
        ]);

        return view('member.medical-summary', compact('record'));
    }

    /**
     * Fast Medicine Lookup API for doctor autocomplete.
     */
    public function lookupMedicines(Request $request)
    {
        $term = trim($request->input('q', ''));
        $query = Medicine::active();

        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('active_ingredient', 'like', "%{$term}%");
            });
        }

        $results = $query->take(20)->get();

        return response()->json($results);
    }

    /**
     * Get patient medical history JSON or partial.
     */
    public function catMedicalHistory(Cat $cat)
    {
        $history = MedicalRecord::where('cat_id', $cat->id)
            ->where('status', 'completed')
            ->with(['doctor', 'primaryDiagnosis', 'prescriptions.items'])
            ->latest('completed_at')
            ->get();

        return response()->json($history);
    }

    /* -------------------------------------------------------------------------
     * Private SOAP Step Processors
     * ------------------------------------------------------------------------- */

    private function saveSubjectiveStep(Request $request, MedicalRecord $record): void
    {
        $request->validate([
            'member_complaint' => 'required|string',
            'symptom_onset' => 'nullable|string|max:100',
            'symptom_history' => 'nullable|string',
            'appetite_history' => 'nullable|string|max:100',
            'drinking_history' => 'nullable|string|max:100',
            'urination_history' => 'nullable|string|max:100',
            'defecation_history' => 'nullable|string|max:100',
            'medication_history' => 'nullable|string',
            'allergy_history' => 'nullable|string',
            'vaccination_history' => 'nullable|string',
            'doctor_clarification' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        $record->update([
            'chief_complaint' => $request->member_complaint,
        ]);

        MedicalRecordSubjective::updateOrCreate(
            ['medical_record_id' => $record->id],
            [
                'member_complaint' => $request->member_complaint,
                'symptom_onset' => $request->symptom_onset,
                'symptom_history' => $request->symptom_history,
                'appetite_history' => $request->appetite_history,
                'drinking_history' => $request->drinking_history,
                'urination_history' => $request->urination_history,
                'defecation_history' => $request->defecation_history,
                'medication_history' => $request->medication_history,
                'allergy_history' => $request->allergy_history,
                'vaccination_history' => $request->vaccination_history,
                'doctor_clarification' => $request->doctor_clarification,
                'additional_notes' => $request->additional_notes,
            ]
        );
    }

    private function saveObjectiveStep(Request $request, MedicalRecord $record): void
    {
        $request->validate([
            'weight' => 'required|numeric|min:0.1|max:30',
            'temperature' => 'required|numeric|min:30|max:45',
            'heart_rate' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'general_condition' => 'required|string|max:100',
            'body_condition_score' => 'nullable|string|max:20',
            'mucous_membrane' => 'nullable|string|max:50',
            'crt' => 'nullable|string|max:20',
            'systems' => 'nullable|array',
            'deworming_given' => 'nullable|boolean',
            'anti_flea_given' => 'nullable|boolean',
            'supplement_given' => 'nullable|boolean',
        ]);

        // Sync legacy columns in medical_records
        $record->update([
            'weight' => $request->weight,
            'temperature' => $request->temperature,
            'general_condition' => $request->general_condition,
            'deworming_given' => $request->boolean('deworming_given'),
            'anti_flea_given' => $request->boolean('anti_flea_given'),
            'supplement_given' => $request->boolean('supplement_given'),
        ]);

        // Standard vital parameters map
        $vitals = [
            'weight' => ['name' => 'Berat Badan', 'val' => $request->weight, 'unit' => 'kg'],
            'temperature' => ['name' => 'Suhu Tubuh', 'val' => $request->temperature, 'unit' => '°C'],
            'heart_rate' => ['name' => 'Denyut Jantung (HR)', 'val' => $request->heart_rate, 'unit' => 'bpm'],
            'respiratory_rate' => ['name' => 'Frekuensi Napas (RR)', 'val' => $request->respiratory_rate, 'unit' => 'rpm'],
            'bcs' => ['name' => 'Body Condition Score (BCS)', 'val' => null, 'unit' => 'skala 1-9', 'text' => $request->body_condition_score],
            'mucous_membrane' => ['name' => 'Selaput Lendir (Mukosa)', 'val' => null, 'unit' => null, 'text' => $request->mucous_membrane],
            'crt' => ['name' => 'Capillary Refill Time (CRT)', 'val' => null, 'unit' => 'detik', 'text' => $request->crt],
        ];

        foreach ($vitals as $code => $data) {
            MedicalRecordObjective::updateOrCreate(
                ['medical_record_id' => $record->id, 'parameter_code' => $code],
                [
                    'parameter_name' => $data['name'],
                    'value_numeric' => $data['val'] ?? null,
                    'value_text' => $data['text'] ?? null,
                    'unit' => $data['unit'] ?? null,
                    'examination_status' => ($data['val'] !== null || !empty($data['text'])) ? 'performed' : 'not_performed',
                    'finding' => 'normal',
                    'source_type' => 'doctor',
                    'observed_at' => now(),
                ]
            );
        }

        // Systematic body examination (eyes, ears, teeth, skin, etc.)
        $systems = $request->input('systems', []);
        $systemLabels = [
            'eyes' => 'Mata (Ophthalmology)',
            'ears' => 'Telinga (Otic)',
            'oral_teeth' => 'Mulut & Gigi (Oral Cavity)',
            'skin_coat' => 'Kulit & Rambut (Dermatology)',
            'musculoskeletal' => 'Muskuloskeletal & Gerak',
            'thoracic_lung' => 'Dada & Paru-paru (Thorax)',
            'abdominal_digestive' => 'Abdomen & Pencernaan',
            'lymph_nodes' => 'Kelenjar Limfonodus',
            'urogenital' => 'Sistem Urogenital & Anus',
        ];

        foreach ($systemLabels as $code => $name) {
            $status = $systems[$code]['status'] ?? 'normal';
            $notes = $systems[$code]['notes'] ?? null;

            MedicalRecordObjective::updateOrCreate(
                ['medical_record_id' => $record->id, 'parameter_code' => "sys_{$code}"],
                [
                    'parameter_name' => $name,
                    'value_numeric' => null,
                    'value_text' => $status,
                    'unit' => null,
                    'examination_status' => 'performed',
                    'finding' => $status,
                    'source_type' => 'doctor',
                    'notes' => $notes,
                    'observed_at' => now(),
                ]
            );
        }
    }

    private function saveAssessmentStep(Request $request, MedicalRecord $record): void
    {
        $request->validate([
            'primary_diagnosis' => 'required|string|max:255',
            'primary_certainty' => 'required|in:suspected,provisional,confirmed',
            'primary_severity' => 'required|in:mild,moderate,severe',
            'clinical_reasoning' => 'nullable|string',
            'secondary_diagnoses' => 'nullable|array',
        ]);

        // Remove previous diagnoses to maintain fresh state
        $record->diagnoses()->delete();

        // 1. Primary Diagnosis
        MedicalRecordDiagnosis::create([
            'medical_record_id' => $record->id,
            'diagnosis_name' => $request->primary_diagnosis,
            'diagnosis_type' => 'primary',
            'certainty' => $request->primary_certainty,
            'severity' => $request->primary_severity,
            'clinical_reasoning' => $request->clinical_reasoning,
            'is_primary' => true,
        ]);

        // 2. Secondary / Differential Diagnoses
        if ($request->filled('secondary_diagnoses')) {
            foreach ($request->secondary_diagnoses as $sec) {
                if (empty($sec['name'])) continue;

                MedicalRecordDiagnosis::create([
                    'medical_record_id' => $record->id,
                    'diagnosis_name' => $sec['name'],
                    'diagnosis_type' => $sec['type'] ?? 'secondary',
                    'certainty' => $sec['certainty'] ?? 'provisional',
                    'severity' => $sec['severity'] ?? 'moderate',
                    'clinical_reasoning' => $sec['notes'] ?? null,
                    'is_primary' => false,
                ]);
            }
        }
    }

    private function savePlanStep(Request $request, MedicalRecord $record): void
    {
        $request->validate([
            'treatment_notes' => 'nullable|string',
            'home_care_instruction' => 'nullable|string',
            'warning_signs' => 'nullable|string',
            'followup_date' => 'nullable|date|after_or_equal:today',
            'followup_reason' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // 1. Sync legacy treatment notes & recommendation
        $record->update([
            'treatment_notes' => $request->treatment_notes,
            'recommendation' => $request->home_care_instruction,
            'internal_notes' => $request->internal_notes,
        ]);

        // 2. Save Clinical Treatment Plan
        if (!empty($request->treatment_notes)) {
            MedicalRecordPlan::updateOrCreate(
                ['medical_record_id' => $record->id, 'plan_type' => 'treatment'],
                [
                    'description' => $request->treatment_notes,
                    'priority' => 'routine',
                    'status' => 'completed',
                ]
            );
        }

        // 3. Save Home Care Advice
        if (!empty($request->home_care_instruction)) {
            MedicalRecordAdvice::updateOrCreate(
                ['medical_record_id' => $record->id, 'category' => 'home_care'],
                [
                    'title' => 'Instruksi Perawatan di Rumah',
                    'instruction' => $request->home_care_instruction,
                    'urgency' => 'routine',
                    'visible_to_member' => true,
                ]
            );
        }

        // 4. Save Warning Signs (Red Flags)
        if (!empty($request->warning_signs)) {
            MedicalRecordAdvice::updateOrCreate(
                ['medical_record_id' => $record->id, 'category' => 'warning_sign'],
                [
                    'title' => 'Tanda Bahaya (Kapan Harus Segera Kembali)',
                    'instruction' => $request->warning_signs,
                    'urgency' => 'urgent',
                    'visible_to_member' => true,
                ]
            );
        }

        // 5. Save Follow-up (Kontrol Lanjutan)
        if (!empty($request->followup_date)) {
            MedicalRecordFollowup::updateOrCreate(
                ['medical_record_id' => $record->id],
                [
                    'followup_type' => $record->service_type,
                    'scheduled_at' => Carbon::parse($request->followup_date),
                    'reason' => $request->followup_reason ?: 'Pemeriksaan evaluasi pasca terapi',
                    'status' => 'planned',
                ]
            );
        }
    }

    /* -------------------------------------------------------------------------
     * Authorizations
     * ------------------------------------------------------------------------- */

    private function authorizeAccess(MedicalRecord $record): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) return;

        if ($user->hasRole('dokter')) {
            // Doctors can access records assigned to them or their clinic
            return;
        }

        abort(403, 'Akses terbatas untuk Dokter Hewan berwenang.');
    }

    private function authorizeEdit(MedicalRecord $record): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$record->canBeEditedBy($user)) {
            abort(403, 'Rekam Medis ini sudah difinalisasi atau Anda tidak memiliki hak ubah.');
        }
    }
}
