<?php

namespace App\Http\Controllers;

use App\Models\Publications;
use Illuminate\Http\Request;
use App\Services\LikeService;

class LikeController extends Controller
{

    protected $likeService;
    
    public function __construct( LikeService $likeService)
    {
        $this->likeService = $likeService;
    }
    
    public function toggleLike(Request $request)
    {
        return $this->likeService->toggleLike($request);
    }
}
