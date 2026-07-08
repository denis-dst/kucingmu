<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Show events management index (admin only).
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show form to create a new event (admin only).
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.events.create');
    }

    /**
     * Store a new event (admin only).
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'registration_link' => 'nullable|string|max:255', // Changed to string for easier custom domains like gentix-apps.com
            'banner' => 'nullable|image|max:2048',
            'status' => 'required|in:active,draft,completed',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'registration_link' => $request->registration_link,
            'banner_path' => $bannerPath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Kegiatan/Event baru berhasil dibuat.');
    }

    /**
     * Show form to edit an event (admin only).
     */
    public function edit(Event $event)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update an event (admin only).
     */
    public function update(Request $request, Event $event)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'registration_link' => 'nullable|string|max:255', // Changed to string for easier custom domains like gentix-apps.com
            'banner' => 'nullable|image|max:2048',
            'status' => 'required|in:active,draft,completed',
        ]);

        $bannerPath = $event->banner_path;
        if ($request->hasFile('banner')) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'registration_link' => $request->registration_link,
            'banner_path' => $bannerPath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Detail Kegiatan/Event berhasil diperbarui.');
    }

    /**
     * Delete an event (admin only).
     */
    public function destroy(Event $event)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($event->banner_path) {
            Storage::disk('public')->delete($event->banner_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Kegiatan/Event berhasil dihapus.');
    }
}
