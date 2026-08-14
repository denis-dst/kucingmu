<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrayCatSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core (step 0)
        'volunteer_id',
        'surveyed_at',
        'campus_location',
        'zone',
        'latitude',
        'longitude',
        'weather',
        'cats_observed',
        'cats_with_ear_tip',
        'cats_needing_attention',
        'food_source',
        'notes',
        'photo_path',

        // Step 0 – Identitas
        'surveyor_name',
        'surveyor_role',
        'start_time',
        'institution',
        'weather_notes',

        // Step 1 – Sensus Visual
        'observation_session',
        'observation_time_range',
        'cats_resighted',
        'cat_individuals',

        // Step 2 – Pemeriksaan Fisik
        'physical_cat_id',
        'physical_cat_name',
        'examining_vet',
        'physical_exam_date',
        'sterilization_status',
        'capture_method',
        'body_weight_kg',
        'rectal_temp_c',
        'heart_rate',
        'resp_rate',
        'dehydration_status',
        'consciousness_level',
        'bcs_score',
        'eye_condition',
        'mucosa_color',
        'ear_condition',
        'nose_condition',
        'mouth_condition',
        'coat_condition',
        'skin_condition',
        'posture_gait',
        'musculoskeletal',
        'abdomen_condition',
        'lymph_nodes',
        'reproductive_condition',
        'welfare_flags',
        'welfare_score',
        'welfare_status',
        'diagnosis_presumptif',
        'follow_up_actions',
        'clinical_notes',

        // Step 3 – Parasit
        'ectoparasite_cat_id',
        'comb_test_result',
        'flea_count',
        'ectoparasite_species',
        'ectoparasite_method',
        'ectoparasite_notes',
        'feces_collection_method',
        'feces_preservation',
        'lab_exam_method',
        'endoparasite_result',
        'zoonotic_agents',
        'endoparasite_notes',

        // Step 4 – Sampel Tanah
        'soil_sample_code',
        'soil_sampling_area',
        'soil_lat',
        'soil_lng',
        'soil_weight_g',
        'soil_depth_cm',
        'soil_condition',
        'feces_visual_indicator',
        'soil_lab_result',
        'soil_parasitic_agents',
        'eggs_per_gram',
        'sanitation_notes',

        // Step 5 – KAP Civitas
        'kap_respondent_status',
        'kap_respondent_gender',
        'kap_faculty',
        'kap_cat_contact',
        'kap_knowledge',
        'kap_attitude',
        'kap_prac_feed',
        'kap_prac_handwash',
        'kap_prac_bite_report',
        'kap_prac_tnrm_support',

        // Step 6 – K3L & SOP
        'k3l_informant_name',
        'k3l_informant_role',
        'k3l_document_checklist',
        'k3l_current_handling',
        'k3l_obstacles',
        'k3l_intervention_plan',
        'k3l_observation_notes',
    ];

    protected $casts = [
        'surveyed_at'             => 'datetime',
        'physical_exam_date'      => 'date',
        // JSON fields
        'cat_individuals'         => 'array',
        'eye_condition'           => 'array',
        'ear_condition'           => 'array',
        'nose_condition'          => 'array',
        'mouth_condition'         => 'array',
        'skin_condition'          => 'array',
        'musculoskeletal'         => 'array',
        'reproductive_condition'  => 'array',
        'welfare_flags'           => 'array',
        'follow_up_actions'       => 'array',
        'ectoparasite_species'    => 'array',
        'lab_exam_method'         => 'array',
        'zoonotic_agents'         => 'array',
        'soil_parasitic_agents'   => 'array',
        'kap_knowledge'           => 'array',
        'kap_attitude'            => 'array',
        'k3l_document_checklist'  => 'array',
        'k3l_current_handling'    => 'array',
        'k3l_obstacles'           => 'array',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}
