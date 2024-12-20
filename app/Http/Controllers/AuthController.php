<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function GithubRedirect()
    {
        return $this->authService->GithubRedirect();
    }

    public function GithubCallback()
    {
        return $this->authService->GithubCallback();
    }
}
