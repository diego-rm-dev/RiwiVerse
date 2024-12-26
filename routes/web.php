<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PublicationController;
use App\Models\Publications;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $unreadNotifications = $user->unreadNotifications;


        $publications = Publications::with('user', 'comments.user', 'likers')
        ->get()
        ->map(function($pub) {
            $pub->isLiked = $pub->likers->contains(auth()->id());
            $pub->likesCount = $pub->likers->count();
            return $pub;
        });     
        return Inertia::render('Dashboard', [
            'publications' => $publications,
            'notifications' => $unreadNotifications, 
        ]);
    })->name('dashboard');
});

Route::get('/profile', function () {
    return Inertia::render('Profile/Show');
})->middleware(['auth:sanctum', 'verified'])->name('profile');

//authentication routes
Route::get('/redirect/github', [ AuthController::class, 'GithubRedirect' ]);
Route::get('/callback/github', [ AuthController::class, 'GithubCallback' ]);

Route::middleware('auth:sanctum')->post('/publications', [PublicationController::class, 'store'])->name('publications.store');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/toggle-like', [LikeController::class, 'toggleLike'])
    ->middleware('auth')
    ->name('toggle-like');

Route::get('/publications/{id}', [PublicationController::class, 'show'])
    ->name('publications.show');
