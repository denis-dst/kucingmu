<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Models\Cat;
use App\Models\MasterWilayah;
use App\Models\StrayCatSurvey;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdoptionController extends Controller
{
    /**
     * Display the Adoption Hub (Adopsi Aku) showcase catalog.
     */
    public function index(Request $request)
    {
        $sourceFilter = $request->get('source', 'all'); // all, member, rescue
        $genderFilter = $request->get('gender', 'all'); // all, male, female
        $statusFilter = $request->get('status', 'available'); // all, available, in_process, adopted
        $wilayahFilter = $request->get('wilayah', 'all');
        $search = trim($request->get('search', ''));

        // 1. Query Member Cats
        $memberCatsQuery = Cat::with(['owner', 'wilayah', 'photos', 'medicalRecords'])
            ->where('is_for_adoption', true);

        if ($statusFilter !== 'all') {
            $memberCatsQuery->where('adoption_status', $statusFilter);
        }

        if ($genderFilter !== 'all') {
            $memberCatsQuery->where('gender', $genderFilter);
        }

        if ($wilayahFilter !== 'all') {
            $memberCatsQuery->where('wilayah_code', $wilayahFilter);
        }

        if (!empty($search)) {
            $memberCatsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('adoption_location', 'like', "%{$search}%")
                  ->orWhere('adoption_notes', 'like', "%{$search}%");
            });
        }

        // 2. Query Rescue / Survey Cats
        $rescueCatsQuery = StrayCatSurvey::with('volunteer')
            ->where('is_for_adoption', true);

        if ($statusFilter !== 'all') {
            $rescueCatsQuery->where('adoption_status', $statusFilter);
        }

        if (!empty($search)) {
            $rescueCatsQuery->where(function ($q) use ($search) {
                $q->where('physical_cat_name', 'like', "%{$search}%")
                  ->orWhere('campus_location', 'like', "%{$search}%")
                  ->orWhere('coat_condition', 'like', "%{$search}%")
                  ->orWhere('adoption_notes', 'like', "%{$search}%");
            });
        }

        // Fetch based on source filter
        $memberCats = collect();
        $rescueCats = collect();

        if (in_array($sourceFilter, ['all', 'member'])) {
            $memberCats = $memberCatsQuery->latest('adoption_listed_at')->get()->map(function ($cat) {
                return (object) [
                    'id' => $cat->id,
                    'source_type' => 'member',
                    'source_label' => '🐱 Kucing Member',
                    'name' => $cat->name,
                    'breed' => $cat->breed ?? 'Domestik',
                    'gender' => $cat->gender,
                    'gender_label' => $cat->gender === 'male' ? 'Jantan ♂' : 'Betina ♀',
                    'age_text' => $cat->age_text,
                    'color' => $cat->color,
                    'photo_url' => $cat->primary_photo_url,
                    'location' => $cat->masked_owner_location,
                    'status' => $cat->adoption_status ?: 'available',
                    'status_label' => $cat->adoption_status_label,
                    'badge_class' => $cat->adoption_badge_class,
                    'notes' => $cat->adoption_notes,
                    'fee_type' => $cat->adoption_fee_type ?: 'Gratis (Bebas Biaya)',
                    'guardian_name' => $cat->masked_owner_name,
                    'unique_code' => $cat->unique_code,
                    'has_medical' => $cat->medicalRecords->isNotEmpty(),
                    'created_at' => $cat->adoption_listed_at ?: $cat->created_at,
                    'raw_model' => $cat,
                ];
            });
        }

        if (in_array($sourceFilter, ['all', 'rescue'])) {
            $rescueCats = $rescueCatsQuery->latest('adoption_listed_at')->get()->map(function ($survey) {
                $name = $survey->physical_cat_name ?: ($survey->campus_location ? 'Kucing ' . $survey->campus_location : 'Kucing Rescue PTMA #' . $survey->id);
                return (object) [
                    'id' => $survey->id,
                    'source_type' => 'rescue',
                    'source_label' => '📋 Kucing Sensus / Rescue',
                    'name' => $name,
                    'breed' => 'Domestik / Rescue',
                    'gender' => null,
                    'gender_label' => 'Rescue',
                    'age_text' => 'Dewasa / Remaja',
                    'color' => $survey->coat_condition ? 'Bulu: ' . substr($survey->coat_condition, 0, 30) : 'Domestik',
                    'photo_url' => $survey->photo_url,
                    'location' => $survey->campus_location ?: 'Kampus PTMA / DIY',
                    'status' => $survey->adoption_status ?: 'available',
                    'status_label' => $survey->adoption_status_label,
                    'badge_class' => $survey->adoption_badge_class,
                    'notes' => $survey->adoption_notes ?: $survey->clinical_notes,
                    'fee_type' => 'Gratis (Bebas Biaya)',
                    'guardian_name' => $survey->masked_volunteer_name,
                    'unique_code' => $survey->physical_cat_id ?: ('RES-' . $survey->id),
                    'has_medical' => !empty($survey->examining_vet),
                    'created_at' => $survey->adoption_listed_at ?: $survey->created_at,
                    'raw_model' => $survey,
                ];
            });
        }

        // Merge and sort
        $allAdoptionCats = $memberCats->concat($rescueCats)->sortByDesc('created_at');

        // Statistics
        $stats = [
            'total_available' => Cat::where('is_for_adoption', true)->where('adoption_status', 'available')->count() +
                                StrayCatSurvey::where('is_for_adoption', true)->where('adoption_status', 'available')->count(),
            'member_cats_count' => Cat::where('is_for_adoption', true)->count(),
            'rescue_cats_count' => StrayCatSurvey::where('is_for_adoption', true)->count(),
            'adopted_count' => Cat::where('is_for_adoption', true)->where('adoption_status', 'adopted')->count() +
                               StrayCatSurvey::where('is_for_adoption', true)->where('adoption_status', 'adopted')->count(),
        ];

        $masterWilayah = MasterWilayah::where('is_active', true)->orderBy('nama')->get();
        $adminContactPhone = AppSetting::get('admin_phone', '081234567890');

        return view('adoption.index', compact(
            'allAdoptionCats',
            'stats',
            'sourceFilter',
            'genderFilter',
            'statusFilter',
            'wilayahFilter',
            'search',
            'masterWilayah',
            'adminContactPhone'
        ));
    }

    /**
     * Display detailed adoption profile of a specific cat.
     */
    public function show(string $source, int $id)
    {
        $adminPhone = AppSetting::get('admin_phone', '6281234567890');
        // Clean admin phone for WhatsApp URL
        $cleanAdminPhone = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($cleanAdminPhone, '0')) {
            $cleanAdminPhone = '62' . substr($cleanAdminPhone, 1);
        }

        if ($source === 'member' || $source === 'cat') {
            $cat = Cat::with(['owner', 'wilayah', 'photos', 'medicalRecords.vet', 'ktamCard'])
                ->where('is_for_adoption', true)
                ->findOrFail($id);

            $catData = (object) [
                'id' => $cat->id,
                'source_type' => 'member',
                'source_label' => '🐱 Kucing Terdaftar dari Member',
                'name' => $cat->name,
                'breed' => $cat->breed ?? 'Domestik',
                'gender' => $cat->gender,
                'gender_label' => $cat->gender === 'male' ? 'Jantan ♂' : 'Betina ♀',
                'age_text' => $cat->age_text,
                'color' => $cat->color,
                'allergies' => $cat->allergies,
                'vaccine_history' => $cat->vaccine_history,
                'photo_url' => $cat->primary_photo_url,
                'all_photos' => $cat->photos,
                'location' => $cat->masked_owner_location,
                'status' => $cat->adoption_status ?: 'available',
                'status_label' => $cat->adoption_status_label,
                'badge_class' => $cat->adoption_badge_class,
                'notes' => $cat->adoption_notes ?: $cat->notes,
                'fee_type' => $cat->adoption_fee_type ?: 'Gratis (Bebas Biaya Adopsi)',
                'guardian_name' => $cat->masked_owner_name,
                'unique_code' => $cat->unique_code ?: ('KM-' . $cat->id),
                'medical_records' => $cat->medicalRecords,
                'raw_cat' => $cat,
            ];
        } else {
            $survey = StrayCatSurvey::with('volunteer')
                ->where('is_for_adoption', true)
                ->findOrFail($id);

            $name = $survey->physical_cat_name ?: ($survey->campus_location ? 'Kucing ' . $survey->campus_location : 'Kucing Rescue PTMA #' . $survey->id);
            $catData = (object) [
                'id' => $survey->id,
                'source_type' => 'rescue',
                'source_label' => '📋 Kucing Hasil Sensus / Rescue Relawan',
                'name' => $name,
                'breed' => 'Domestik / Rescue',
                'gender' => null,
                'gender_label' => 'Rescue PTMA',
                'age_text' => 'Remaja / Dewasa',
                'color' => $survey->coat_condition ?: 'Domestik',
                'allergies' => null,
                'vaccine_history' => null,
                'photo_url' => $survey->photo_url,
                'all_photos' => collect([ (object)['photo_path' => $survey->photo_path, 'label' => 'Foto Temuan'] ]),
                'location' => $survey->campus_location ?: 'Lingkungan Kampus PTMA',
                'status' => $survey->adoption_status ?: 'available',
                'status_label' => $survey->adoption_status_label,
                'badge_class' => $survey->adoption_badge_class,
                'notes' => $survey->adoption_notes ?: $survey->clinical_notes,
                'fee_type' => 'Gratis (Bebas Biaya Adopsi)',
                'guardian_name' => $survey->masked_volunteer_name,
                'unique_code' => $survey->physical_cat_id ?: ('RES-' . $survey->id),
                'medical_records' => collect(),
                'raw_survey' => $survey,
            ];
        }

        // WhatsApp Admin mediation URL
        $waMsg = rawurlencode("Halo Admin KucingMu, saya tertarik untuk mengajukan adopsi anabul {$catData->name} (Kode: {$catData->unique_code}). Mohon informasi prosedur adopsi dan jadwal temu.");
        $adminWaUrl = "https://wa.me/{$cleanAdminPhone}?text={$waMsg}";

        return view('adoption.show', compact('catData', 'adminWaUrl', 'cleanAdminPhone'));
    }

    /**
     * Submit an adoption application from a prospective adopter.
     */
    public function apply(Request $request, string $source, int $id)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:30',
            'applicant_city' => 'required|string|max:100',
            'applicant_address' => 'nullable|string|max:1000',
            'housing_type' => 'required|string|max:100',
            'has_other_pets' => 'nullable|string|max:255',
            'has_family_consent' => 'required',
            'commitment_notes' => 'required|string|min:10|max:2000',
        ], [
            'applicant_name.required' => 'Nama lengkap pemohon wajib diisi.',
            'applicant_email.required' => 'Alamat email wajib diisi.',
            'applicant_phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'applicant_city.required' => 'Kota domisili wajib diisi.',
            'housing_type.required' => 'Tipe tempat tinggal wajib dipilih.',
            'commitment_notes.required' => 'Alasan & komitmen merawat anabul wajib diisi.',
            'commitment_notes.min' => 'Mohon jelaskan komitmen merawat minimal 10 karakter.',
        ]);

        $appCode = AdoptionApplication::generateCode();
        $user = Auth::user();

        if ($source === 'member' || $source === 'cat') {
            $cat = Cat::where('is_for_adoption', true)->findOrFail($id);
            $catName = $cat->name;
            $catId = $cat->id;
            $surveyId = null;
            $catSource = 'member_cat';

            // Mark cat status as in_process if it was available
            if ($cat->adoption_status === 'available') {
                $cat->update(['adoption_status' => 'in_process']);
            }
        } else {
            $survey = StrayCatSurvey::where('is_for_adoption', true)->findOrFail($id);
            $catName = $survey->physical_cat_name ?: 'Kucing Rescue #' . $survey->id;
            $catId = null;
            $surveyId = $survey->id;
            $catSource = 'stray_survey';

            if ($survey->adoption_status === 'available') {
                $survey->update(['adoption_status' => 'in_process']);
            }
        }

        $application = AdoptionApplication::create([
            'application_code' => $appCode,
            'user_id' => $user ? $user->id : null,
            'cat_id' => $catId,
            'stray_cat_survey_id' => $surveyId,
            'cat_source' => $catSource,
            'cat_name' => $catName,
            'applicant_name' => trim($request->applicant_name),
            'applicant_email' => trim($request->applicant_email),
            'applicant_phone' => trim($request->applicant_phone),
            'applicant_city' => trim($request->applicant_city),
            'applicant_address' => trim($request->applicant_address ?: ''),
            'housing_type' => trim($request->housing_type),
            'has_other_pets' => trim($request->has_other_pets ?: 'Tidak Ada'),
            'has_family_consent' => (bool) $request->has_family_consent,
            'commitment_notes' => trim($request->commitment_notes),
            'status' => 'pending',
        ]);

        Log::info("Pengajuan Adopsi baru diterima [{$appCode}] untuk kucing '{$catName}' dari '{$request->applicant_name}' ({$request->applicant_email}).");

        return redirect()->route('adoption.show', [$source, $id])
            ->with('adoption_success', [
                'code' => $appCode,
                'name' => $request->applicant_name,
                'cat_name' => $catName,
            ]);
    }
}
