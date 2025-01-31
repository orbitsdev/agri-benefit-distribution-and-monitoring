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
use App\Http\Controllers\ReportController;
use App\Filament\Barangay\Pages\ListOfBeneficiaries;
use App\Livewire\CodeFormPage;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {

        $user = Auth::user();

        switch ($user->role) {
            case User::SUPER_ADMIN:
                return redirect('/admin');
                break;
            case User::ADMIN:
                return redirect('/barangay');
                break;
            case User::MEMBER:
                // Check if the user has a code
                if (!empty($user->code)) {
                    return redirect()->route('member.dashboard'); // Redirect to Member Dashboard
                } else {
                    return redirect()->route('support-login'); // Redirect to Support Login
                }
                break;
            default:
                return view('dashboard');
                break;
        }

    })->name('dashboard');

    Route::middleware([ 'check.support.code'])->group(function () {
        Route::get('/member-dashboard', MemberDashboard::class)->name('member.dashboard');
    });
    
    Route::middleware([ 'redirect.if.has.code'])->group(function () {
        Route::get('/support/login', CodeFormPage::class)->name('support-login');
    });


    Route::get('/reports/barangay-distributions', [ReportController::class, 'exportBarangayDistributions'])
    ->name('reports.barangay-distributions');

    Route::get('/reports/system-users', [ReportController::class, 'exportSystemUsers'])
    ->name('reports.system-users');


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





