<?php

namespace App\Http\Controllers;

use App\Models\Galerie;
use Illuminate\Http\Request;

class GalerieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galeries = \App\Models\Galerie::all();
        return view('galeries.index', compact('galeries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('galeries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()->route('galeries.index')
            ->with('success', 'Média ajouté à la galerie.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Galerie $galerie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Galerie $galerie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Galerie $galerie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Galerie $galerie)
    {
        //
    }
}
