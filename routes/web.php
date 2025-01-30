<?php

use App\Mail\QrMail;
use App\Models\User;
use App\Livewire\Test;
use App\Models\Beneficiary;
use App\Livewire\MemberDashboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Filament\Barangay\Pages\ListOfBeneficiaries;
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

    // Route::get('chat/', ListOfBeneficiaries::class);
});

Route::get('/test-qr-mail', function () {
    try {
        $beneficiary = Beneficiary::with(['distributionItem.distribution', 'distributionItem.item'])->first();

        if (!$beneficiary) {
            return "No beneficiary found.";
        }

        Mail::to($beneficiary->email)->send(new QrMail($beneficiary));

        return "QR email sent to {$beneficiary->email}";
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error sending QR email: ' . $e->getMessage());

        return "Failed to send email. Check the logs for details.";
    }
});
