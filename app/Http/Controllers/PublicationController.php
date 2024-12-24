<?php

namespace App\Http\Controllers;

use App\Services\PublicationService;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    private $publicationService;

    public function __construct( PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    public function store(Request $request){
        $this->publicationService->store($request);
    }

    public function index(){
        $this->publicationService->index();
    }
}
