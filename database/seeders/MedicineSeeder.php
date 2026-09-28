<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Clavamox / Amoxicillin Clavulanate',
                'active_ingredient' => 'Amoxicillin Trihydrate + Potassium Clavulanate',
                'dosage_form' => 'tablet',
                'concentration_value' => 62.50,
                'concentration_unit' => 'mg/tab',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Antibiotik spektrum luas untuk infeksi saluran pernapasan (URTI), kulit, dan saluran kemih kucing.',
            ],
            [
                'name' => 'Clavamox Drop Oral Suspension',
                'active_ingredient' => 'Amoxicillin + Clavulanic Acid',
                'dosage_form' => 'sirup',
                'concentration_value' => 62.50,
                'concentration_unit' => 'mg/ml',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Suspensi oral antibiotik yang mudah diberikan dengan spuit/dropper untuk anak kucing (kitten) dan dewasa.',
            ],
            [
                'name' => 'Doxycat / Doxycycline Vet',
                'active_ingredient' => 'Doxycycline Hyclate',
                'dosage_form' => 'tablet',
                'concentration_value' => 50.00,
                'concentration_unit' => 'mg/tab',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Antibiotik lini pertama untuk Chlamydia felis, Mycoplasma felis, dan Haemobartonellosis. Selalu beri air setelah pemberian.',
            ],
            [
                'name' => 'Metronidazole Oral Suspension',
                'active_ingredient' => 'Metronidazole Benzoate',
                'dosage_form' => 'sirup',
                'concentration_value' => 125.00,
                'concentration_unit' => 'mg/5ml',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Anti-protozoa dan antibiotik anaerobik untuk giardiasis, trikomoniasis, dan diare kronis.',
            ],
            [
                'name' => 'Meloxicam Feline Oral (Metacam)',
                'active_ingredient' => 'Meloxicam',
                'dosage_form' => 'sirup',
                'concentration_value' => 0.50,
                'concentration_unit' => 'mg/ml',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'NSAID anti-inflamasi dan analgesik terukur khusus kucing. Diberikan bersama atau sesudah makan.',
            ],
            [
                'name' => 'Tolfedine Cat & Dog',
                'active_ingredient' => 'Tolfenamic Acid',
                'dosage_form' => 'tablet',
                'concentration_value' => 6.00,
                'concentration_unit' => 'mg/tab',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Pereda demam tinggi, nyeri sendi, dan inflamasi akut pada kucing.',
            ],
            [
                'name' => 'Drontal Cat Dewormer',
                'active_ingredient' => 'Pyrantel Embonate (230mg) + Praziquantel (20mg)',
                'dosage_form' => 'tablet',
                'concentration_value' => 1.00,
                'concentration_unit' => 'tab/4kg',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Obat cacing spektrum luas untuk cacing gelang, cacing kait, dan cacing pita (tapeworm) pada kucing.',
            ],
            [
                'name' => 'Revolution Cat (Selamectin 6%)',
                'active_ingredient' => 'Selamectin',
                'dosage_form' => 'spot-on',
                'concentration_value' => 45.00,
                'concentration_unit' => 'mg/tube',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Obat tetes tengkuk pencegah kutu pinjal (flea), ear mites (tungau telinga), scabies, dan cacing jantung.',
            ],
            [
                'name' => 'Advocate Cat (< 4kg & > 4kg)',
                'active_ingredient' => 'Imidacloprid (10%) + Moxidectin (1.0%)',
                'dosage_form' => 'spot-on',
                'concentration_value' => 0.80,
                'concentration_unit' => 'ml/tube',
                'species_scope' => json_encode(['cat']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Spot-on antiparasit eksternal dan internal efektif untuk earmites, pinjal, dan pencegahan larva.',
            ],
            [
                'name' => 'Ciloxan / Ciprofloxacin Ophthalmic',
                'active_ingredient' => 'Ciprofloxacin HCl 0.3%',
                'dosage_form' => 'tetes',
                'concentration_value' => 0.30,
                'concentration_unit' => '%',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Tetes mata steril untuk konjungtivitis bakterial, ulkus kornea, dan mata belekan/merah.',
            ],
            [
                'name' => 'Tarivid Otic Drops',
                'active_ingredient' => 'Ofloxacin 3mg/ml',
                'dosage_form' => 'tetes',
                'concentration_value' => 3.00,
                'concentration_unit' => 'mg/ml',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Tetes telinga untuk otitis eksterna dan infeksi telinga tengah bakterial.',
            ],
            [
                'name' => 'Ketoconazole + Miconazole Cream',
                'active_ingredient' => 'Ketoconazole 2%',
                'dosage_form' => 'salep',
                'concentration_value' => 2.00,
                'concentration_unit' => '%',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Salep topikal antijamur untuk ringworm (Microsporum canis) dan dermatitis Malassezia.',
            ],
            [
                'name' => 'Nutri-Plus Gel Virbac',
                'active_ingredient' => 'Multivitamin, mineral, asam lemak esensial',
                'dosage_form' => 'salep',
                'concentration_value' => 120.00,
                'concentration_unit' => 'gram/tube',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Pasta gel nutrisi tinggi kalori untuk masa pemulihan, kucing anoreksia, atau pasca-operasi.',
            ],
            [
                'name' => 'Lactulose Syrup',
                'active_ingredient' => 'Lactulose',
                'dosage_form' => 'sirup',
                'concentration_value' => 3.30,
                'concentration_unit' => 'g/5ml',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => false,
                'is_active' => true,
                'description' => 'Pencahar osmotik aman untuk konstipasi, megakolon, dan manajemen ensefalopati hepatik.',
            ],
            [
                'name' => 'Cerenia Solution (Maropitant)',
                'active_ingredient' => 'Maropitant Citrate',
                'dosage_form' => 'injeksi',
                'concentration_value' => 10.00,
                'concentration_unit' => 'mg/ml',
                'species_scope' => json_encode(['cat', 'dog']),
                'prescription_required' => true,
                'is_active' => true,
                'description' => 'Antiemetik poten antagonis reseptor NK1 untuk menghentikan muntah akut dan pencegahan mabuk perjalanan.',
            ],
        ];

        foreach ($medicines as $m) {
            DB::table('medicines')->updateOrInsert(
                ['name' => $m['name']],
                array_merge($m, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
