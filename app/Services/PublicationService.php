<?php

namespace App\Services;

use App\Models\Publications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicationService{


    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'hashtags' => 'nullable|string',
            'status' => 'required|in:published,deleted',
            'title' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|image|max:2048',
        ]);
    
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('publications', 'public');
                $imagePaths[] = Storage::url($path);
            }
        }
    
        Publications::create(array_merge($validated, [
            'images' => $imagePaths,
            'date' => Carbon::now()->toDateString(), // Generar la fecha actual
            'user_id' => auth()->id(),
        ]));
    
        return back()->with('success', 'Publicación creada exitosamente.');
    }
    

    public function index()
    {
        try {
            $publications = Publications::with('user')->get()->map(function ($publication) {
                $publication->images = collect($publication->images)->map(fn($image) => Storage::url($image));
                return $publication;
            });
    
            return inertia('Dashboard', [
                'publications' => $publications,
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id){
        $publication = Publications::with('comments.user', 'likers', 'user')
        ->findOrFail($id);

        return inertia('Publications/Sho    w', [
        'publication' => $publication,
    ]);
    }
    
    
    
}