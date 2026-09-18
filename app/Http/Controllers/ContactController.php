<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Show the contact page for visitors and members.
     */
    public function index()
    {
        $this->generateCaptcha();

        $contactEmail = AppSetting::get('contact_email', 'bidkes.immdiy@gmail.com / kucingmuhammadiyah@gmail.com');
        $officeAddress = AppSetting::get('office_address', 'Jl. Gedongkuning No.130 B, Rejowinangun, Kec. Kotagede, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55171');

        return view('contact', compact('contactEmail', 'officeAddress'));
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $expectedCaptcha = session('contact_captcha');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'captcha' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($expectedCaptcha) {
                    if ($expectedCaptcha === null || (int)$value !== (int)$expectedCaptcha) {
                        $fail('Jawaban verifikasi keamanan (Captcha) tidak sesuai. Silakan coba kembali.');
                    }
                }
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek atau judul pesan wajib diisi.',
            'message.required' => 'Isi pesan wajib diisi.',
            'captcha.required' => 'Verifikasi keamanan (Captcha) wajib diisi.',
        ]);

        ContactMessage::create([
            'user_id' => Auth::id(),
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'phone' => $request->phone ? trim($request->phone) : null,
            'subject' => trim($request->subject),
            'message' => trim($request->message),
            'status' => 'unread',
        ]);

        // Generate a new captcha challenge after submission
        $this->generateCaptcha();

        return redirect()->back()->with('contact_success', 'Pesan Anda berhasil dikirim! Tim administrator KucingMu akan segera meninjau dan merespon pesan Anda.');
    }

    /**
     * Helper to generate a simple math captcha and store in session.
     */
    public static function generateCaptcha(): void
    {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session([
            'contact_captcha' => $num1 + $num2,
            'contact_captcha_question' => "Berapakah {$num1} + {$num2}?",
        ]);
    }
}
