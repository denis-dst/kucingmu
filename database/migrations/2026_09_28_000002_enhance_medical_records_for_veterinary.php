<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Enhance medical_records table
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'record_number')) {
                $table->string('record_number', 50)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('medical_records', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('cat_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('medical_records', 'service_type')) {
                $table->string('service_type', 30)->default('clinic')->after('vet_id'); // clinic, online
            }
            if (!Schema::hasColumn('medical_records', 'clinic_name')) {
                $table->string('clinic_name', 150)->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('medical_records', 'status')) {
                $table->string('status', 30)->default('completed')->after('clinic_name'); // draft, in_progress, awaiting_tests, completed, cancelled
            }
            if (!Schema::hasColumn('medical_records', 'chief_complaint')) {
                $table->text('chief_complaint')->nullable()->after('status');
            }
            if (!Schema::hasColumn('medical_records', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('recommendation');
            }
            if (!Schema::hasColumn('medical_records', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('internal_notes');
            }
            if (!Schema::hasColumn('medical_records', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('medical_records', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            }
        });

        // 2. Master medicines table
        if (!Schema::hasTable('medicines')) {
            Schema::create('medicines', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('active_ingredient')->nullable();
                $table->string('dosage_form')->default('tablet'); // tablet, sirup, injeksi, salep, tetes, spot-on
                $table->decimal('concentration_value', 10, 2)->nullable();
                $table->string('concentration_unit', 30)->nullable(); // mg/tab, mg/ml, %
                $table->json('species_scope')->nullable(); // ["cat", "dog"]
                $table->boolean('prescription_required')->default(true);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['is_active', 'name']);
            });
        }

        // 3. SOAP: Subjective table
        if (!Schema::hasTable('medical_record_subjectives')) {
            Schema::create('medical_record_subjectives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->text('member_complaint')->nullable(); // Keluhan asli member
                $table->string('symptom_onset')->nullable(); // e.g. "3 hari yang lalu"
                $table->text('symptom_history')->nullable(); // Perkembangan gejala
                $table->string('appetite_history')->nullable(); // Normal, Menurun, Anoreksia
                $table->string('drinking_history')->nullable(); // Normal, Polidipsia, Menurun
                $table->string('urination_history')->nullable(); // Normal, Disuria, Hematuria, Anuria
                $table->string('defecation_history')->nullable(); // Normal, Diare, Konstipasi
                $table->text('medication_history')->nullable(); // Riwayat obat
                $table->text('allergy_history')->nullable(); // Riwayat alergi
                $table->text('vaccination_history')->nullable(); // Riwayat vaksin
                $table->text('doctor_clarification')->nullable(); // Catatan klarifikasi dokter
                $table->text('additional_notes')->nullable();
                $table->timestamps();

                $table->unique('medical_record_id');
            });
        }

        // 4. SOAP: Objective table
        if (!Schema::hasTable('medical_record_objectives')) {
            Schema::create('medical_record_objectives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->string('parameter_code', 50); // weight, temp, hr, rr, bcs, mucous, eyes, ears, etc.
                $table->string('parameter_name', 100);
                $table->decimal('value_numeric', 8, 2)->nullable();
                $table->string('value_text')->nullable();
                $table->string('unit', 30)->nullable();
                $table->string('examination_status', 30)->default('performed'); // performed, not_performed, unavailable
                $table->string('finding', 50)->nullable(); // normal, abnormal, not_evaluated
                $table->string('source_type', 30)->default('doctor'); // doctor, owner_reported, external_result
                $table->timestamp('observed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['medical_record_id', 'parameter_code']);
            });
        }

        // 5. SOAP: Diagnoses table
        if (!Schema::hasTable('medical_record_diagnoses')) {
            Schema::create('medical_record_diagnoses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->string('diagnosis_code', 50)->nullable();
                $table->string('diagnosis_name');
                $table->string('diagnosis_type', 30)->default('primary'); // primary, secondary, differential
                $table->string('certainty', 30)->default('provisional'); // suspected, provisional, confirmed
                $table->string('severity', 30)->nullable(); // mild, moderate, severe
                $table->text('clinical_reasoning')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->index(['medical_record_id', 'is_primary']);
            });
        }

        // 6. SOAP: Plans table (treatment & clinical procedures)
        if (!Schema::hasTable('medical_record_plans')) {
            Schema::create('medical_record_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->string('plan_type', 30)->default('treatment'); // treatment, procedure, test, referral
                $table->text('description');
                $table->string('priority', 30)->default('routine'); // routine, urgent, emergency
                $table->timestamp('planned_at')->nullable();
                $table->string('status', 30)->default('planned'); // planned, in_progress, completed, cancelled
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['medical_record_id', 'plan_type']);
            });
        }

        // 7. Electronic Prescriptions table
        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->string('prescription_number', 50)->unique();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->foreignId('prescribed_by')->constrained('users')->cascadeOnDelete();
                $table->string('status', 30)->default('draft'); // draft, issued, cancelled, superseded
                $table->timestamp('issued_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->foreignId('replaces_prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
                $table->timestamps();

                $table->index(['medical_record_id', 'status']);
            });
        }

        // 8. Prescription items table
        if (!Schema::hasTable('prescription_items')) {
            Schema::create('prescription_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
                $table->foreignId('medicine_id')->nullable()->constrained('medicines')->nullOnDelete();
                $table->string('medicine_name_snapshot');
                $table->string('active_ingredient')->nullable();
                $table->string('dosage_form', 50)->default('tablet');
                $table->decimal('concentration_value', 10, 2)->nullable();
                $table->string('concentration_unit', 30)->nullable();
                $table->decimal('dose_value', 10, 2)->nullable();
                $table->string('dose_unit', 30)->nullable();
                $table->string('dose_basis', 30)->default('fixed'); // fixed, per_kg, other
                $table->string('administration_route', 50)->default('oral'); // oral, topikal, subkutan, dll.
                $table->string('frequency_value', 50)->nullable(); // e.g. "2x sehari"
                $table->string('frequency_unit', 50)->nullable();
                $table->string('duration_value', 50)->nullable(); // e.g. "5"
                $table->string('duration_unit', 50)->nullable(); // hari, minggu
                $table->decimal('quantity_value', 10, 2)->nullable(); // e.g. 10
                $table->string('quantity_unit', 50)->nullable(); // tablet, botol
                $table->text('usage_instructions')->nullable();
                $table->text('warnings')->nullable();
                $table->timestamps();

                $table->index('prescription_id');
            });
        }

        // 9. Home-care Advice & Warning Signs table
        if (!Schema::hasTable('medical_record_advices')) {
            Schema::create('medical_record_advices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->string('category', 50)->default('home_care'); // home_care, nutrition, warning_sign, activity, other
                $table->string('title');
                $table->text('instruction');
                $table->string('urgency', 30)->default('routine'); // routine, prompt, urgent
                $table->boolean('visible_to_member')->default(true);
                $table->timestamps();

                $table->index(['medical_record_id', 'category']);
            });
        }

        // 10. Follow-ups (Jadwal Kontrol) table
        if (!Schema::hasTable('medical_record_followups')) {
            Schema::create('medical_record_followups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->string('followup_type', 30)->default('clinic'); // clinic, online, test, referral
                $table->timestamp('scheduled_at')->nullable();
                $table->text('reason')->nullable();
                $table->string('status', 30)->default('planned'); // planned, booked, completed, cancelled
                $table->foreignId('followup_record_id')->nullable()->constrained('medical_records')->nullOnDelete();
                $table->text('outcome_notes')->nullable();
                $table->timestamps();

                $table->index(['medical_record_id', 'status']);
            });
        }

        // 11. Audit logs table (Koreksi, Finalisasi, Amendemen)
        if (!Schema::hasTable('medical_record_audit_logs')) {
            Schema::create('medical_record_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
                $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
                $table->string('action', 50); // created, updated_draft, issued_prescription, finalized, amended, cancelled
                $table->string('entity_type', 100)->nullable();
                $table->string('entity_id', 100)->nullable();
                $table->text('reason')->nullable();
                $table->json('changes')->nullable();
                $table->string('request_id', 100)->nullable();
                $table->timestamp('occurred_at')->useCurrent();

                $table->index(['medical_record_id', 'occurred_at']);
            });
        }

        // Backfill record_number and member_id for existing records
        $existingRecords = \Illuminate\Support\Facades\DB::table('medical_records')->get();
        foreach ($existingRecords as $rec) {
            $cat = \Illuminate\Support\Facades\DB::table('cats')->where('id', $rec->cat_id)->first();
            $recNumber = 'MR-' . date('Ym', strtotime($rec->created_at ?? 'now')) . '-' . str_pad($rec->id, 4, '0', STR_PAD_LEFT);
            \Illuminate\Support\Facades\DB::table('medical_records')
                ->where('id', $rec->id)
                ->update([
                    'record_number' => $rec->record_number ?: $recNumber,
                    'member_id' => $rec->member_id ?: ($cat ? $cat->user_id : null),
                    'status' => $rec->status ?: 'completed',
                    'service_type' => $rec->service_type ?: 'clinic',
                    'completed_at' => $rec->completed_at ?: ($rec->created_at ?? now()),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_audit_logs');
        Schema::dropIfExists('medical_record_followups');
        Schema::dropIfExists('medical_record_advices');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('medical_record_plans');
        Schema::dropIfExists('medical_record_diagnoses');
        Schema::dropIfExists('medical_record_objectives');
        Schema::dropIfExists('medical_record_subjectives');

        Schema::table('medical_records', function (Blueprint $table) {
            $cols = [
                'record_number',
                'member_id',
                'service_type',
                'clinic_name',
                'status',
                'chief_complaint',
                'internal_notes',
                'started_at',
                'completed_at',
                'created_by'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('medical_records', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
