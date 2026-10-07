<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Cat;
use App\Models\StrayCatSurvey;
use App\Models\EmailOutbox;
use App\Mail\AdminDirectMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminAdoptionController extends Controller
{
    /**
     * Display Adoption Hub management & listed cats.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
        $sourceFilter = $request->get('source', 'all');

        $memberCatsQuery = Cat::with(['owner', 'wilayah', 'photos'])->where('is_for_adoption', true);
        $rescueCatsQuery = StrayCatSurvey::with('volunteer')->where('is_for_adoption', true);

        if ($statusFilter !== 'all') {
            $memberCatsQuery->where('adoption_status', $statusFilter);
            $rescueCatsQuery->where('adoption_status', $statusFilter);
        }

        $memberCats = $memberCatsQuery->latest('adoption_listed_at')->get();
        $rescueCats = $rescueCatsQuery->latest('adoption_listed_at')->get();

        $stats = [
            'total_listed' => Cat::where('is_for_adoption', true)->count() + StrayCatSurvey::where('is_for_adoption', true)->count(),
            'available' => Cat::where('is_for_adoption', true)->where('adoption_status', 'available')->count() +
                           StrayCatSurvey::where('is_for_adoption', true)->where('adoption_status', 'available')->count(),
            'in_process' => Cat::where('is_for_adoption', true)->where('adoption_status', 'in_process')->count() +
                            StrayCatSurvey::where('is_for_adoption', true)->where('adoption_status', 'in_process')->count(),
            'adopted' => Cat::where('is_for_adoption', true)->where('adoption_status', 'adopted')->count() +
                         StrayCatSurvey::where('is_for_adoption', true)->where('adoption_status', 'adopted')->count(),
            'pending_applications' => AdoptionApplication::where('status', 'pending')->count(),
        ];

        return view('admin.adoption.index', compact('memberCats', 'rescueCats', 'stats', 'statusFilter', 'sourceFilter'));
    }

    /**
     * Display all adoption applications from prospective adopters.
     */
    public function applications(Request $request)
    {
        $query = AdoptionApplication::with(['cat.owner', 'strayCatSurvey.volunteer', 'applicant', 'reviewer'])->latest();

        $statusFilter = $request->get('status', 'all');
        if (in_array($statusFilter, ['pending', 'reviewing', 'approved', 'rejected', 'completed', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('applicant_email', 'like', "%{$search}%")
                  ->orWhere('applicant_phone', 'like', "%{$search}%")
                  ->orWhere('applicant_city', 'like', "%{$search}%")
                  ->orWhere('cat_name', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => AdoptionApplication::count(),
            'pending' => AdoptionApplication::where('status', 'pending')->count(),
            'reviewing' => AdoptionApplication::where('status', 'reviewing')->count(),
            'approved' => AdoptionApplication::where('status', 'approved')->count(),
            'completed' => AdoptionApplication::where('status', 'completed')->count(),
            'rejected' => AdoptionApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.adoption.applications', compact('applications', 'stats', 'statusFilter'));
    }

    /**
     * Display a specific adoption application detail for mediation.
     */
    public function showApplication(AdoptionApplication $application)
    {
        $application->load(['cat.owner', 'cat.wilayah', 'strayCatSurvey.volunteer', 'applicant', 'reviewer']);

        return view('admin.adoption.application-show', compact('application'));
    }

    /**
     * Update adoption application status and notes.
     */
    public function updateApplicationStatus(Request $request, AdoptionApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,approved,rejected,completed,cancelled',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $admin = Auth::user();
        $targetStatus = $request->status;

        $application->update([
            'status' => $targetStatus,
            'admin_notes' => $request->admin_notes ? trim($request->admin_notes) : $application->admin_notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        // Sync cat status if completed or approved
        if ($targetStatus === 'completed') {
            if ($application->cat) {
                $application->cat->update(['adoption_status' => 'adopted']);
            } elseif ($application->strayCatSurvey) {
                $application->strayCatSurvey->update(['adoption_status' => 'adopted']);
            }
        } elseif ($targetStatus === 'approved') {
            if ($application->cat) {
                $application->cat->update(['adoption_status' => 'in_process']);
            } elseif ($application->strayCatSurvey) {
                $application->strayCatSurvey->update(['adoption_status' => 'in_process']);
            }
        }

        return redirect()->back()->with('success', "Status pengajuan [{$application->application_code}] berhasil diperbarui menjadi: " . $application->status_label);
    }

    /**
     * Toggle adoption listing for a member cat.
     */
    public function toggleCatAdoption(Request $request, Cat $cat)
    {
        $isForAdoption = $request->has('is_for_adoption') ? (bool) $request->is_for_adoption : !$cat->is_for_adoption;
        $status = $request->input('adoption_status', 'available');
        $notes = $request->input('adoption_notes', $cat->adoption_notes);
        $location = $request->input('adoption_location', $cat->adoption_location);

        $cat->update([
            'is_for_adoption' => $isForAdoption,
            'adoption_status' => $status,
            'adoption_notes' => $notes,
            'adoption_location' => $location,
            'adoption_listed_at' => $isForAdoption ? ($cat->adoption_listed_at ?: now()) : null,
        ]);

        $msg = $isForAdoption ? "Kucing {$cat->name} sekarang DIBUKA untuk adopsi di etalase 'Adopsi Aku'." : "Kucing {$cat->name} telah DIHAPUS dari etalase adopsi.";
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete an adoption application.
     */
    public function destroyApplication(AdoptionApplication $application)
    {
        $code = $application->application_code;
        $application->delete();

        return redirect()->route('admin.adoptions.applications')->with('success', "Pengajuan adopsi [{$code}] berhasil dihapus.");
    }
}
