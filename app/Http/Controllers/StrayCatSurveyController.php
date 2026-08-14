<?php

namespace App\Http\Controllers;

use App\Models\StrayCatSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StrayCatSurveyController extends Controller
{
    public function index()
    {
        $surveys = StrayCatSurvey::where('volunteer_id', Auth::id())
            ->latest('surveyed_at')
            ->paginate(10);

        return view('volunteer.surveillance', compact('surveys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // ── Step 0 ──
            'surveyed_at'              => ['required', 'date'],
            'start_time'               => ['nullable', 'string', 'max:10'],
            'surveyor_name'            => ['nullable', 'string', 'max:255'],
            'surveyor_role'            => ['nullable', 'string', 'max:255'],
            'institution'              => ['nullable', 'string', 'max:255'],
            'campus_location'          => ['required', 'string', 'max:255'],
            'zone'                     => ['required', 'string', 'max:255'],
            'latitude'                 => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'                => ['nullable', 'numeric', 'between:-180,180'],
            'weather'                  => ['nullable', 'in:cerah,berawan,hujan ringan,hujan lebat'],
            'weather_notes'            => ['nullable', 'string', 'max:2000'],

            // ── Step 1 ──
            'cats_observed'            => ['required', 'integer', 'min:0', 'max:999'],
            'cats_with_ear_tip'        => ['nullable', 'integer', 'min:0'],
            'cats_needing_attention'   => ['nullable', 'integer', 'min:0'],
            'cats_resighted'           => ['nullable', 'integer', 'min:0'],
            'observation_session'      => ['nullable', 'string', 'max:50'],
            'observation_time_range'   => ['nullable', 'string', 'max:50'],
            'food_source'              => ['nullable', 'string', 'max:255'],
            'cat_individuals'          => ['nullable', 'string'],           // JSON from hidden field

            // ── Step 2 ──
            'physical_cat_id'          => ['nullable', 'string', 'max:50'],
            'physical_cat_name'        => ['nullable', 'string', 'max:100'],
            'examining_vet'            => ['nullable', 'string', 'max:100'],
            'physical_exam_date'       => ['nullable', 'date'],
            'sterilization_status'     => ['nullable', 'string', 'max:50'],
            'capture_method'           => ['nullable', 'string', 'max:100'],
            'body_weight_kg'           => ['nullable', 'numeric', 'min:0', 'max:20'],
            'rectal_temp_c'            => ['nullable', 'numeric', 'min:35', 'max:42'],
            'heart_rate'               => ['nullable', 'integer', 'min:0'],
            'resp_rate'                => ['nullable', 'integer', 'min:0'],
            'dehydration_status'       => ['nullable', 'string', 'max:50'],
            'consciousness_level'      => ['nullable', 'string', 'max:50'],
            'bcs_score'                => ['nullable', 'integer', 'min:1', 'max:9'],
            'eye_condition'            => ['nullable', 'array'],
            'eye_condition.*'          => ['string'],
            'mucosa_color'             => ['nullable', 'string', 'max:50'],
            'ear_condition'            => ['nullable', 'array'],
            'ear_condition.*'          => ['string'],
            'nose_condition'           => ['nullable', 'array'],
            'nose_condition.*'         => ['string'],
            'mouth_condition'          => ['nullable', 'array'],
            'mouth_condition.*'        => ['string'],
            'coat_condition'           => ['nullable', 'string', 'max:50'],
            'skin_condition'           => ['nullable', 'array'],
            'skin_condition.*'         => ['string'],
            'posture_gait'             => ['nullable', 'string', 'max:50'],
            'musculoskeletal'          => ['nullable', 'array'],
            'musculoskeletal.*'        => ['string'],
            'abdomen_condition'        => ['nullable', 'string', 'max:50'],
            'lymph_nodes'              => ['nullable', 'string', 'max:50'],
            'reproductive_condition'   => ['nullable', 'array'],
            'reproductive_condition.*' => ['string'],
            'welfare_score'            => ['nullable', 'integer', 'min:0', 'max:8'],
            'welfare_status'           => ['nullable', 'string', 'max:50'],
            'welfare_flags'            => ['nullable', 'string'],           // JSON from hidden field
            'diagnosis_presumptif'     => ['nullable', 'string', 'max:255'],
            'follow_up_actions'        => ['nullable', 'array'],
            'follow_up_actions.*'      => ['string'],
            'clinical_notes'           => ['nullable', 'string', 'max:3000'],

            // ── Step 3 ──
            'ectoparasite_cat_id'      => ['nullable', 'string', 'max:50'],
            'comb_test_result'         => ['nullable', 'in:Negatif,Positif'],
            'flea_count'               => ['nullable', 'integer', 'min:0'],
            'ectoparasite_species'     => ['nullable', 'array'],
            'ectoparasite_species.*'   => ['string'],
            'ectoparasite_method'      => ['nullable', 'string', 'max:100'],
            'ectoparasite_notes'       => ['nullable', 'string', 'max:2000'],
            'feces_collection_method'  => ['nullable', 'string', 'max:50'],
            'feces_preservation'       => ['nullable', 'string', 'max:100'],
            'lab_exam_method'          => ['nullable', 'array'],
            'lab_exam_method.*'        => ['string'],
            'endoparasite_result'      => ['nullable', 'in:Negatif,Positif,Pending Lab'],
            'zoonotic_agents'          => ['nullable', 'array'],
            'zoonotic_agents.*'        => ['string'],
            'endoparasite_notes'       => ['nullable', 'string', 'max:2000'],

            // ── Step 4 ──
            'soil_sample_code'         => ['nullable', 'string', 'max:50'],
            'soil_sampling_area'       => ['nullable', 'string', 'max:100'],
            'soil_lat'                 => ['nullable', 'numeric', 'between:-90,90'],
            'soil_lng'                 => ['nullable', 'numeric', 'between:-180,180'],
            'soil_weight_g'            => ['nullable', 'numeric', 'min:1'],
            'soil_depth_cm'            => ['nullable', 'numeric', 'min:0'],
            'soil_condition'           => ['nullable', 'string', 'max:20'],
            'feces_visual_indicator'   => ['nullable', 'in:Ya,Tidak'],
            'soil_lab_result'          => ['nullable', 'in:Negatif,Positif,Pending'],
            'soil_parasitic_agents'    => ['nullable', 'array'],
            'soil_parasitic_agents.*'  => ['string'],
            'eggs_per_gram'            => ['nullable', 'integer', 'min:0'],
            'sanitation_notes'         => ['nullable', 'string', 'max:2000'],

            // ── Step 5 ──
            'kap_respondent_status'    => ['nullable', 'string', 'max:50'],
            'kap_respondent_gender'    => ['nullable', 'string', 'max:20'],
            'kap_faculty'              => ['nullable', 'string', 'max:100'],
            'kap_cat_contact'          => ['nullable', 'in:Ya,Tidak'],
            'kap_knowledge'            => ['nullable', 'string'],            // JSON
            'kap_attitude'             => ['nullable', 'string'],            // JSON
            'kap_prac_feed'            => ['nullable', 'string', 'max:50'],
            'kap_prac_handwash'        => ['nullable', 'string', 'max:50'],
            'kap_prac_bite_report'     => ['nullable', 'string', 'max:50'],
            'kap_prac_tnrm_support'    => ['nullable', 'string', 'max:50'],

            // ── Step 6 ──
            'k3l_informant_name'       => ['nullable', 'string', 'max:100'],
            'k3l_informant_role'       => ['nullable', 'string', 'max:100'],
            'k3l_document_checklist'   => ['nullable', 'string'],            // JSON
            'k3l_current_handling'     => ['nullable', 'array'],
            'k3l_current_handling.*'   => ['string'],
            'k3l_obstacles'            => ['nullable', 'array'],
            'k3l_obstacles.*'          => ['string'],
            'k3l_intervention_plan'    => ['nullable', 'string', 'max:3000'],
            'k3l_observation_notes'    => ['nullable', 'string', 'max:3000'],

            // Photo
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('stray-surveys', 'public');
        }

        // Decode JSON fields sent from JS
        foreach (['cat_individuals', 'welfare_flags', 'kap_knowledge', 'kap_attitude', 'k3l_document_checklist'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);
                $data[$field] = is_array($decoded) ? $decoded : null;
            }
        }

        $data['volunteer_id'] = Auth::id();

        StrayCatSurvey::create($data);

        return redirect()->route('volunteer.surveillance.index')
            ->with('success', 'Laporan eSurveillance kucing liar berhasil disimpan.');
    }
}
