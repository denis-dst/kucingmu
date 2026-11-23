<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use App\Models\CatCondolence;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatCondolenceController extends Controller
{
    /**
     * Show Cat Memorial & Condolences page.
     */
    public function show(Cat $cat)
    {
        $cat->load([
            'owner',
            'photos',
            'ktamCard',
            'wilayah',
            'condolences.user',
        ]);

        return view('member.cat-memorial', compact('cat'));
    }

    /**
     * Submit a condolence message from a member to a deceased cat's guardian.
     */
    public function store(Request $request, Cat $cat)
    {
        $request->validate([
            'message' => 'required|string|min:3|max:1000',
            'prayer_text' => 'nullable|string|max:255',
        ]);

        $sender = Auth::user();

        $condolence = CatCondolence::create([
            'cat_id' => $cat->id,
            'user_id' => $sender->id,
            'message' => $request->message,
            'prayer_text' => $request->prayer_text,
            'is_visible' => true,
        ]);

        // Dispatch notification to the cat's owner
        if ((int) $cat->user_id !== (int) $sender->id && $cat->owner) {
            UserNotification::make(
                $cat->user_id,
                'Ucapan Belasungkawa untuk ' . $cat->name,
                "{$sender->name} telah mengirimkan ucapan belasungkawa dan simpati untuk anabul tercinta Anda, {$cat->name}.",
                route('cat.memorial', $cat->id),
                'cat_condolence',
                $sender->id,
                'heart',
                [
                    'cat_id' => $cat->id,
                    'cat_name' => $cat->name,
                    'condolence_id' => $condolence->id,
                ]
            );
        }

        return redirect()->route('cat.memorial', $cat->id)
            ->with('success', 'Ucapan belasungkawa dan doa Anda berhasil disampaikan kepada pemilik anabul.');
    }

    /**
     * Render Twibbon / Memorial Poster for deceased cat.
     */
    public function poster(Cat $cat)
    {
        $cat->load([
            'owner',
            'photos',
            'ktamCard',
            'wilayah',
            'condolences.user',
        ]);

        return view('member.cat-memorial-poster', compact('cat'));
    }
}
