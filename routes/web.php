<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {

        switch(Auth::user()->role){
            case User::SUPER_ADMIN:
                return redirect('/admin');
                break;
                // case User::ADMIN:
                // return redirect()->route('buyer.dashboard',['name'=> Auth::user()->fullNameSlug()]);
                // break;
                // case User::MEMBER:
                // return redirect('/farmer');
                // break;
            default:
              return view('dashboard');
                break;
        }

    })->name('dashboard');
});
