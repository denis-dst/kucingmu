<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MedicalRecordAuditLog;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    /**
     * Display Pharmacy workspace and prescription fulfillment queue.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isFarmasi() && !$user->isAdmin()) {
            abort(403, 'Akses terbatas untuk Tenaga Farmasi dan Apoteker.');
        }

        $status = $request->input('status', 'waiting'); // 'waiting', 'dispensed', 'all'
        $search = trim($request->input('search', ''));

        $query = Prescription::whereIn('status', ['issued', 'dispensed'])
            ->with([
                'medicalRecord.cat.owner',
                'medicalRecord.cat.photos',
                'medicalRecord.doctor',
                'medicalRecord.primaryDiagnosis',
                'items.medicine',
                'dispenser',
            ]);

        if ($status === 'waiting') {
            $query->whereNull('dispensed_at')->where('status', 'issued');
        } elseif ($status === 'dispensed') {
            $query->where(function ($q) {
                $q->whereNotNull('dispensed_at')->orWhere('status', 'dispensed');
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('prescription_number', 'like', "%{$search}%")
                  ->orWhereHas('medicalRecord.cat', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('medicalRecord.cat.owner', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('medicine_name_snapshot', 'like', "%{$search}%")
                         ->orWhere('racikan_name', 'like', "%{$search}%");
                  });
            });
        }

        $prescriptions = $query->latest('issued_at')->paginate(15)->withQueryString();

        $stats = [
            'waiting_count' => Prescription::where('status', 'issued')->whereNull('dispensed_at')->count(),
            'dispensed_count' => Prescription::whereNotNull('dispensed_at')->count(),
            'today_count' => Prescription::whereDate('issued_at', Carbon::today())->count(),
        ];

        return view('farmasi.prescriptions.index', compact('prescriptions', 'stats', 'status', 'search'));
    }

    /**
     * Show prescription details for fulfillment.
     */
    public function show(Prescription $prescription)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isFarmasi() && !$user->isAdmin()) {
            abort(403, 'Akses terbatas untuk Tenaga Farmasi dan Apoteker.');
        }

        $prescription->load([
            'medicalRecord.cat.owner',
            'medicalRecord.cat.photos',
            'medicalRecord.cat.ktamCard',
            'medicalRecord.doctor',
            'medicalRecord.primaryDiagnosis',
            'medicalRecord.advices',
            'items.medicine',
            'dispenser',
        ]);

        return view('farmasi.prescriptions.show', compact('prescription'));
    }

    /**
     * Mark prescription as dispensed / given to member.
     */
    public function dispense(Request $request, Prescription $prescription)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isFarmasi() && !$user->isAdmin()) {
            abort(403, 'Akses terbatas untuk Tenaga Farmasi dan Apoteker.');
        }

        $request->validate([
            'dispense_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();

            $prescription->update([
                'status' => 'dispensed',
                'dispensed_at' => $now,
                'dispensed_by' => $user->id,
                'dispense_notes' => $request->dispense_notes,
            ]);

            // Update item-level flags
            $prescription->items()->update([
                'is_dispensed' => true,
                'dispensed_at' => $now,
            ]);

            $record = $prescription->medicalRecord;
            $cat = $record?->cat;
            $owner = $cat?->owner;

            // Audit log in medical record if present
            if ($record) {
                MedicalRecordAuditLog::log(
                    $record,
                    $user,
                    'dispensed',
                    "Resep #{$prescription->prescription_number} telah diserahkan kepada pemilik kucing ({$owner?->name}) oleh Tenaga Farmasi ({$user->name})." .
                    ($request->dispense_notes ? " Catatan: {$request->dispense_notes}" : "")
                );
            }

            // Dispatch notification to member
            if ($owner) {
                UserNotification::make(
                    $owner->id,
                    'Obat Resep Telah Diserahkan',
                    "Resep obat untuk kucing kesayangan Anda, {$cat->name}, telah diserahkan oleh Petugas Farmasi ({$user->name}). Silakan periksa aturan pakai pada buku rekam medis.",
                    route('member.medical-records.summary', $record->id),
                    'prescription_dispensed',
                    $user->id,
                    'pill',
                    ['prescription_id' => $prescription->id, 'medical_record_id' => $record?->id]
                );
            }

            DB::commit();

            return redirect()->route('farmasi.prescriptions.index')
                ->with('success', "Resep #{$prescription->prescription_number} untuk pasien {$cat?->name} berhasil ditandai sebagai: Sudah Diberikan kepada Member.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui status dispensasi farmasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses penyerahan obat: ' . $e->getMessage());
        }
    }
}
