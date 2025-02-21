<?php
use App\Mail\QrMail;
use App\Models\User;
use App\Livewire\Test;
use App\Models\Support;
use App\Models\Beneficiary;
use App\Livewire\CodeFormPage;
use App\Livewire\QrScannerPage;
use App\Livewire\MemberDashboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Livewire\SupportNotAuthorize;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Livewire\ScannerSupportEnterCodePage;
use App\Filament\Barangay\Pages\ListOfBeneficiaries;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::post('/support/logout', function () {
        $user = Auth::user();

        // Remove support code from the user session
        $user->update(['code' => null]);

        return redirect()->route('support-login')->with('success', 'You have exited Support Mode.');
    })->name('support.logout')->middleware('auth');
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
                if (request()->route()->getName() !== 'support.dashboard') {
                    return redirect()->route('support.dashboard');
                }
                break;
            default:
                return view('dashboard');
                break;
        }

    })->name('dashboard');


    Route::middleware(['check.support.code'])->get('/support/dashboard', function () {
        $user = Auth::user();

        $support = Support::where('unique_code', $user->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->first();

        if (!$support) {
            // Clear invalid code to prevent redirect loop
            $user->update(['code' => null]);
            return redirect()->route('support-login')->with('error', 'Invalid or missing support code.');
        }

        if ($support->enable_beneficiary_management && $support->enable_item_scanning) {
            return redirect()->route('member.dashboard')->with('success', 'You have access to both Beneficiary Management and Scanning.');
        }

        if ($support->enable_beneficiary_management) {
            return redirect()->route('member.dashboard');
        }

        if ($support->enable_item_scanning) {
            return redirect()->route('qr-scan');
        }

        return redirect()->route('support-not-authorize')->with('error', 'No valid permissions assigned.');
    })->name('support.dashboard');


    Route::middleware([ 'check.support.login'])->group(function () {
        Route::get('/support/login', CodeFormPage::class)->name('support-login');
    });


    Route::middleware(['check.member.permissions'])->group(function () {
        Route::get('/member-dashboard', MemberDashboard::class)->name('member.dashboard');
    });


    Route::middleware(['check.scanner.permissions'])->group(function () {
        Route::get('/scan-qr', QrScannerPage::class)->name('qr-scan');
    });


    Route::get('/support-not-authorize', SupportNotAuthorize::class)->name('support-not-authorize');

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





