<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminGalerieController extends Controller
{
    public function index()
    {
        $galeries = \App\Models\Galerie::orderByDesc('created_at')->get();
        return view('admin/galeries/index', compact('galeries'));
    }

    public function create()
    {
        return view('admin/galeries/create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|in:photo,video',
            'media' => 'required_if:type,photo|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'video' => 'required_if:type,video|file|mimes:mp4,webm,ogg|max:51200',
            'description' => 'nullable|string',
        ]);

        $path = null;
        if ($request->type === 'photo' && $request->hasFile('media')) {
            $path = $request->file('media')->store('galerie/photos', 'public');
        } elseif ($request->type === 'video' && $request->hasFile('video')) {
            $path = $request->file('video')->store('galerie/videos', 'public');
        }

        \App\Models\Galerie::create([
            'titre' => $request->titre,
            'type' => $request->type,
            'url' => $path,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.galeries.index')->with('success', 'Média ajouté à la galerie.');
    }
}

