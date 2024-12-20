<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthService{

    public function GithubRedirect(){
        return Socialite::driver('github')->redirect();
    }

    public function GithubCallback(){
        $githubUser = Socialite::driver('github')->user();

        $userExists = User::where('email', $githubUser->email)->first();

        if($userExists){
            Auth::login($userExists);
            return redirect('/dashboard');
        }else{
            $user = User::create([
                'name' => $githubUser->name,
                'email' => $githubUser->email,
                'password' => bcrypt('password'),
            ]);

            Auth::login($user);
            return redirect('/dashboard');
        }

        return redirect('/dashboard');
    }

}