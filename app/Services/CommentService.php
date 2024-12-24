<?php

namespace App\Services;

use App\Models\Comments;
use Illuminate\Http\Request;
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

        return back()->with([
            'success' => 'Comentario creado exitosamente.',
            'newComment' => $comment
        ]);

    }

}