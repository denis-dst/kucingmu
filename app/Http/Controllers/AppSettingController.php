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

        // Process text and select/boolean settings
        $settings = $request->input('settings', []);
        foreach ($settings as $key => $value) {
            $setting = AppSetting::find($key);
            if ($setting && $setting->type !== 'file') {
                if ($setting->type === 'boolean' && $value === null) {
                    $value = '0';
                }
                $setting->update(['value' => $value]);
            }
        }

        // Process file settings (logo, favicon, etc.)
        if ($request->hasFile('settings')) {
            $files = $request->file('settings');
            foreach ($files as $key => $file) {
                $setting = AppSetting::find($key);
                if ($setting && $setting->type === 'file') {
                    // Delete old file if exists
                    if ($setting->value) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->value);
                    }
                    // Store new file
                    $path = $file->store('settings', 'public');
                    $setting->update(['value' => $path]);
                }
            }
        }

        return redirect()->back()->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }
}
