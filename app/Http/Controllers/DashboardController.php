<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\KtamCard;
use App\Models\User;
use App\Services\KtamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard based on user role.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isDokter()) {
            return $this->dokterDashboard();
        } elseif ($user->isVolunteer()) {
            return $this->volunteerDashboard();
        } else {
            return $this->memberDashboard();
        }
    }

    /**
     * Render Admin Dashboard.
     */
    protected function adminDashboard()
    {
        $stats = [
            'cats_count' => Cat::count(),
            'appointments_count' => Appointment::count(),
            'records_count' => MedicalRecord::count(),
            'ktam_count' => KtamCard::count(),
        ];

        $cats = Cat::with('owner', 'ktamCard')->latest()->paginate(10);
        $appointments = Appointment::with('cat')->orderBy('date', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'cats', 'appointments'));
    }

    /**
     * Render Vet Dashboard.
     */
    protected function dokterDashboard()
    {
        // Queue for today
        $queue = Appointment::with('cat.owner')
            ->whereIn('status', ['scheduled', 'checked_in'])
            ->whereDate('date', Carbon::today())
            ->orderBy('status', 'desc') // checked_in first
            ->orderBy('id', 'asc')
            ->get();

        $recentRecords = MedicalRecord::with('cat', 'appointment')
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
        $todayAppointments = Appointment::with('cat.owner')
            ->whereDate('date', Carbon::today())
            ->orderBy('id', 'desc')
            ->get();

        return view('volunteer.dashboard', compact('todayAppointments'));
    }

    /**
     * Render Member Dashboard.
     */
    protected function memberDashboard()
    {
        $cats = Auth::user()->cats()->with('ktamCard', 'medicalRecords')->get();
        $appointments = Appointment::whereIn('cat_id', $cats->pluck('id'))
            ->with('cat')
            ->latest()
            ->get();

        return view('member.dashboard', compact('cats', 'appointments'));
    }

    /**
     * Store a new cat profile (for Member).
     */
    public function storeCat(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('cats', 'public');
        }

        Cat::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'photo_path' => $photoPath,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil dibuat.');
    }

    /**
     * Show the edit cat profile form (for Member).
     */
    public function editCat(Cat $cat)
    {
        // Check permission: owner only
        if ($cat->user_id !== Auth::id()) {
            abort(403);
        }

        return view('member.edit-cat', compact('cat'));
    }

    /**
     * Update the cat profile (for Member).
     */
    public function updateCat(Request $request, Cat $cat)
    {
        // Check permission: owner only
        if ($cat->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'allergies' => 'nullable|string',
            'vaccine_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $photoPath = $cat->photo_path;
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($cat->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cat->photo_path);
            }
            $photoPath = $request->file('photo')->store('cats', 'public');
        }

        $cat->update([
            'name' => $request->name,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'photo_path' => $photoPath,
            'allergies' => $request->allergies,
            'vaccine_history' => $request->vaccine_history,
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil kucing berhasil diperbarui.');
    }

    /**
     * Book a health checkup appointment (for Member).
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'cat_id' => 'required|exists:cats,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Verify ownership
        $cat = Cat::findOrFail($request->cat_id);
        if ($cat->user_id !== Auth::id()) {
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
     * Store clinical check-up and automatically issue KTAM (for Dokter).
     */
    public function storeCheckup(Request $request, Appointment $appointment, KtamService $ktamService)
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

        // Create Medical Record
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

        // Update Appointment status to completed
        $appointment->update(['status' => 'completed']);

        // Issue KTAM Card for the cat
        $ktamService->issueCard($appointment->cat);

        return redirect()->route('dashboard')->with('success', 'Rekam medis berhasil disimpan & Kartu KTAM diterbitkan.');
    }

    /**
     * Download digital KTAM Card as PDF.
     */
    public function downloadKtam(Cat $cat)
    {
        $card = $cat->ktamCard;
        if (!$card) {
            return redirect()->route('dashboard')->with('error', 'Kucing ini belum memiliki kartu KTAM.');
        }

        // Check permission: admin, volunteer, vet, or owner
        if (Auth::user()->role === 'member' && $cat->user_id !== Auth::id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.ktam', compact('card', 'cat'));
        // 86mm x 54mm equivalent in points: 243.78 x 153.07
        $pdf->setPaper([0, 0, 243.78, 153.07], 'portrait');
        
        return $pdf->stream('ktam_' . str_replace(' ', '_', strtolower($cat->name)) . '.pdf');
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
            'cat_gender' => 'required|in:male,female',
            'cat_dob' => 'required|date',
        ]);

        // Create Member User
        $owner = User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => bcrypt('kucingmu123'), // Default password
            'phone' => $request->owner_phone,
            'role' => 'member',
            'muhammadiyah_id' => $request->owner_nbm,
        ]);

        // Create Cat
        $cat = Cat::create([
            'user_id' => $owner->id,
            'name' => $request->cat_name,
            'breed' => $request->cat_breed,
            'gender' => $request->cat_gender,
            'date_of_birth' => $request->cat_dob,
        ]);

        // Automatically create a scheduled check-up for today
        Appointment::create([
            'cat_id' => $cat->id,
            'date' => Carbon::today(),
            'time_slot' => 'On-site Registration',
            'status' => 'checked_in', // Check-in immediately
            'notes' => 'Registrasi langsung di lokasi event.',
        ]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Kucing langsung masuk antrian.');
    }

    /**
     * KTAM Verification Page (Public scan landing).
     */
    public function verifyKtam($number)
    {
        $card = KtamCard::where('ktam_number', $number)->firstOrFail();
        $cat = $card->cat()->with('owner')->first();

        // Retrieve checkup history
        $records = MedicalRecord::where('cat_id', $cat->id)->with('vet')->latest()->get();

        return view('ktam-verify', compact('card', 'cat', 'records'));
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

        $cards = KtamCard::with('cat.owner')->get();

        $callback = function() use($cards) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['KTAM Number', 'Cat Name', 'Breed', 'Gender', 'Owner Name', 'Owner NBM', 'Owner Phone', 'Issue Date']);

            foreach ($cards as $card) {
                fputcsv($file, [
                    $card->ktam_number,
                    $card->cat->name,
                    $card->cat->breed,
                    $card->cat->gender,
                    $card->cat->owner->name,
                    $card->cat->owner->muhammadiyah_id ?? '-',
                    $card->cat->owner->phone ?? '-',
                    $card->issue_date->format('Y-m-d'),
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

            $cat = Cat::create([
                'user_id' => $owner->id,
                'name' => $entry['cat_name'],
                'breed' => $entry['cat_breed'],
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
}
