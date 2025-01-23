<?php

use App\Livewire\MemberDashboard;
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
            // case User::MEMBER:
            //     return view('/dashboard');
            //     break;
                case User::ADMIN:
                return redirect('/barangay');
                break;
                // case User::MEMBER:
                // return redirect('/farmer');
                // break;
            default:
              return view('dashboard');
                break;
        }

    })->name('dashboard');

    Route::get('/member-dashboard', MemberDashboard::class )->name('member.dashboard');
});
