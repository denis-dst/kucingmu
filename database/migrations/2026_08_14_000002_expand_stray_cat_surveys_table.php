<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            // Step 0 – Identitas Surveyor
            $table->string('surveyor_name')->nullable()->after('volunteer_id');
            $table->string('surveyor_role')->nullable()->after('surveyor_name');       // Profesi/Peran
            $table->time('start_time')->nullable()->after('surveyor_role');
            $table->string('institution')->nullable()->after('start_time');
            $table->string('weather_notes')->nullable()->after('weather');             // catatan cuaca/lokasi

            // Step 1 – Sensus Visual
            $table->string('observation_session')->nullable()->after('weather_notes'); // Sesi 1–5
            $table->string('observation_time_range')->nullable()->after('observation_session'); // pagi/siang/sore/malam
            $table->unsignedInteger('cats_resighted')->default(0)->after('observation_time_range');
            $table->json('cat_individuals')->nullable()->after('cats_resighted');      // JSON: data tiap kucing

            // Step 2 – Pemeriksaan Fisik
            $table->string('physical_cat_id')->nullable()->after('cat_individuals');
            $table->string('physical_cat_name')->nullable()->after('physical_cat_id');
            $table->string('examining_vet')->nullable()->after('physical_cat_name');
            $table->date('physical_exam_date')->nullable()->after('examining_vet');
            $table->string('sterilization_status')->nullable()->after('physical_exam_date');
            $table->string('capture_method')->nullable()->after('sterilization_status');
            // Vital signs
            $table->decimal('body_weight_kg', 5, 2)->nullable()->after('capture_method');
            $table->decimal('rectal_temp_c', 4, 1)->nullable()->after('body_weight_kg');
            $table->unsignedSmallInteger('heart_rate')->nullable()->after('rectal_temp_c');
            $table->unsignedSmallInteger('resp_rate')->nullable()->after('heart_rate');
            $table->string('dehydration_status')->nullable()->after('resp_rate');
            $table->string('consciousness_level')->nullable()->after('dehydration_status');
            $table->unsignedTinyInteger('bcs_score')->nullable()->after('consciousness_level'); // 1–9
            // Organ systems (JSON arrays)
            $table->json('eye_condition')->nullable()->after('bcs_score');
            $table->string('mucosa_color')->nullable()->after('eye_condition');
            $table->json('ear_condition')->nullable()->after('mucosa_color');
            $table->json('nose_condition')->nullable()->after('ear_condition');
            $table->json('mouth_condition')->nullable()->after('nose_condition');
            $table->string('coat_condition')->nullable()->after('mouth_condition');
            $table->json('skin_condition')->nullable()->after('coat_condition');
            $table->string('posture_gait')->nullable()->after('skin_condition');
            $table->json('musculoskeletal')->nullable()->after('posture_gait');
            $table->string('abdomen_condition')->nullable()->after('musculoskeletal');
            $table->string('lymph_nodes')->nullable()->after('abdomen_condition');
            $table->json('reproductive_condition')->nullable()->after('lymph_nodes');
            // Welfare
            $table->json('welfare_flags')->nullable()->after('reproductive_condition');  // {0:0,1:1,...}
            $table->unsignedTinyInteger('welfare_score')->nullable()->after('welfare_flags');
            $table->string('welfare_status')->nullable()->after('welfare_score');
            // Clinical conclusion
            $table->string('diagnosis_presumptif')->nullable()->after('welfare_status');
            $table->json('follow_up_actions')->nullable()->after('diagnosis_presumptif');
            $table->text('clinical_notes')->nullable()->after('follow_up_actions');

            // Step 3 – Parasit
            $table->string('ectoparasite_cat_id')->nullable()->after('clinical_notes');
            $table->string('comb_test_result')->nullable()->after('ectoparasite_cat_id');  // Negatif/Positif
            $table->unsignedSmallInteger('flea_count')->nullable()->after('comb_test_result');
            $table->json('ectoparasite_species')->nullable()->after('flea_count');
            $table->string('ectoparasite_method')->nullable()->after('ectoparasite_species');
            $table->text('ectoparasite_notes')->nullable()->after('ectoparasite_method');
            $table->string('feces_collection_method')->nullable()->after('ectoparasite_notes');
            $table->string('feces_preservation')->nullable()->after('feces_collection_method');
            $table->json('lab_exam_method')->nullable()->after('feces_preservation');
            $table->string('endoparasite_result')->nullable()->after('lab_exam_method');    // Negatif/Positif/Pending
            $table->json('zoonotic_agents')->nullable()->after('endoparasite_result');
            $table->text('endoparasite_notes')->nullable()->after('zoonotic_agents');

            // Step 4 – Sampel Tanah
            $table->string('soil_sample_code')->nullable()->after('endoparasite_notes');
            $table->string('soil_sampling_area')->nullable()->after('soil_sample_code');
            $table->decimal('soil_lat', 10, 7)->nullable()->after('soil_sampling_area');
            $table->decimal('soil_lng', 10, 7)->nullable()->after('soil_lat');
            $table->decimal('soil_weight_g', 6, 1)->nullable()->after('soil_lng');
            $table->decimal('soil_depth_cm', 4, 1)->nullable()->after('soil_weight_g');
            $table->string('soil_condition')->nullable()->after('soil_depth_cm');           // Kering/Lembap/Basah
            $table->string('feces_visual_indicator')->nullable()->after('soil_condition');  // Ya/Tidak
            $table->string('soil_lab_result')->nullable()->after('feces_visual_indicator'); // Negatif/Positif/Pending
            $table->json('soil_parasitic_agents')->nullable()->after('soil_lab_result');
            $table->unsignedInteger('eggs_per_gram')->nullable()->after('soil_parasitic_agents');
            $table->text('sanitation_notes')->nullable()->after('eggs_per_gram');

            // Step 5 – KAP Civitas
            $table->string('kap_respondent_status')->nullable()->after('sanitation_notes');
            $table->string('kap_respondent_gender')->nullable()->after('kap_respondent_status');
            $table->string('kap_faculty')->nullable()->after('kap_respondent_gender');
            $table->string('kap_cat_contact')->nullable()->after('kap_faculty');             // Ya/Tidak
            $table->json('kap_knowledge')->nullable()->after('kap_cat_contact');             // knowledge answers
            $table->json('kap_attitude')->nullable()->after('kap_knowledge');                // attitude Likert scores
            $table->string('kap_prac_feed')->nullable()->after('kap_attitude');
            $table->string('kap_prac_handwash')->nullable()->after('kap_prac_feed');
            $table->string('kap_prac_bite_report')->nullable()->after('kap_prac_handwash');
            $table->string('kap_prac_tnrm_support')->nullable()->after('kap_prac_bite_report');

            // Step 6 – K3L & SOP
            $table->string('k3l_informant_name')->nullable()->after('kap_prac_tnrm_support');
            $table->string('k3l_informant_role')->nullable()->after('k3l_informant_name');
            $table->json('k3l_document_checklist')->nullable()->after('k3l_informant_role'); // {item:Ada/Tidak Ada/Dalam Proses}
            $table->json('k3l_current_handling')->nullable()->after('k3l_document_checklist');
            $table->json('k3l_obstacles')->nullable()->after('k3l_current_handling');
            $table->text('k3l_intervention_plan')->nullable()->after('k3l_obstacles');
            $table->text('k3l_observation_notes')->nullable()->after('k3l_intervention_plan');
        });
    }

    public function down(): void
    {
        Schema::table('stray_cat_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'surveyor_name','surveyor_role','start_time','institution','weather_notes',
                'observation_session','observation_time_range','cats_resighted','cat_individuals',
                'physical_cat_id','physical_cat_name','examining_vet','physical_exam_date',
                'sterilization_status','capture_method','body_weight_kg','rectal_temp_c',
                'heart_rate','resp_rate','dehydration_status','consciousness_level','bcs_score',
                'eye_condition','mucosa_color','ear_condition','nose_condition','mouth_condition',
                'coat_condition','skin_condition','posture_gait','musculoskeletal','abdomen_condition',
                'lymph_nodes','reproductive_condition','welfare_flags','welfare_score','welfare_status',
                'diagnosis_presumptif','follow_up_actions','clinical_notes',
                'ectoparasite_cat_id','comb_test_result','flea_count','ectoparasite_species',
                'ectoparasite_method','ectoparasite_notes','feces_collection_method','feces_preservation',
                'lab_exam_method','endoparasite_result','zoonotic_agents','endoparasite_notes',
                'soil_sample_code','soil_sampling_area','soil_lat','soil_lng','soil_weight_g',
                'soil_depth_cm','soil_condition','feces_visual_indicator','soil_lab_result',
                'soil_parasitic_agents','eggs_per_gram','sanitation_notes',
                'kap_respondent_status','kap_respondent_gender','kap_faculty','kap_cat_contact',
                'kap_knowledge','kap_attitude','kap_prac_feed','kap_prac_handwash',
                'kap_prac_bite_report','kap_prac_tnrm_support',
                'k3l_informant_name','k3l_informant_role','k3l_document_checklist',
                'k3l_current_handling','k3l_obstacles','k3l_intervention_plan','k3l_observation_notes',
            ]);
        });
    }
};
