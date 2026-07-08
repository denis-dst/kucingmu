<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppSettingController extends Controller
{
    /**
     * Show app settings editor (admin only).
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $settings = AppSetting::all();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $settings = $request->input('settings', []);
        foreach ($settings as $key => $value) {
            $setting = AppSetting::find($key);
            if ($setting) {
                // If the setting is boolean type and we didn't receive it, we check
                if ($setting->type === 'boolean' && $value === null) {
                    $value = '0';
                }
                $setting->update(['value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }
}
