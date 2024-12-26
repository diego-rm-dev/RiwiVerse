<?php

namespace App\Services;

use App\Models\Comments;
use Illuminate\Http\Request;
use Inertia\Inertia;

Class CommentService{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:500',
            'status' => 'required|in:published,deleted',
            'publication_id' => 'required|integer|exists:publications,id',
            'date' => 'required|date',
        ]);
    
        $comment = Comments::create([
            'text' => $validated['text'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'user_id' => auth()->id(),
            'publication_id' => $validated['publication_id'],
        ]);
    
        // Verifica si es una solicitud AJAX (Inertia.js)
        if ($request->header('X-Inertia')) {
            // Devolver un componente de Inertia con datos actualizados
            return Inertia::render('Dashboard', [
                'success' => 'Comentario creado exitosamente.',
                'newComment' => $comment->load('user'),
            ]);
        }
        // Si no es una solicitud AJAX, redirige normalmente
        return back()->with('success', 'Comentario creado exitosamente.');
    }
    
    

}