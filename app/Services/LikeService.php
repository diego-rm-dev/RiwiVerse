<?php 

namespace App\Services;

use App\Models\Publications;
use App\Notifications\NewLikeNotification;
use Illuminate\Http\Request;

class LikeService {

    public function toggleLike(Request $request)
    {
        $publicationId = $request->input('publication_id');
        $publication = Publications::findOrFail($publicationId);
        $userId = auth()->id();

        $alreadyLiked = $publication->likers()->where('user_id', $userId)->exists();

        if ($alreadyLiked) {
            $publication->likers()->detach($userId);
        } else {
            $publication->likers()->attach($userId);

            $publicationOwner = $publication->user;

            if ($publicationOwner->id !== $userId) {
                $publicationOwner->notify(
                    new NewLikeNotification($publication)
                );
            }
    
        }

        return back()->with('success', 'Like toggled correctamente.');
    }
}