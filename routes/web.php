<?php
use App\Mail\QrMail;
use App\Models\User;
use App\Livewire\Test;
use App\Models\Support;
use App\Models\Beneficiary;
use App\Livewire\CodeFormPage;

use App\Livewire\MemberDashboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Livewire\Staff\QrScannerPage;
use App\Livewire\SupportNotAuthorize;
use Illuminate\Support\Facades\Route;
use App\Livewire\Staff\StaffDashboard;
use App\Livewire\Staff\BeneficiaryList;
use App\Livewire\Staff\TransactionList;
use App\Http\Controllers\ReportController;
use App\Livewire\ScannerSupportEnterCodePage;
use App\Livewire\Staff\BarangayDistributionList;
use App\Filament\Barangay\Pages\ListOfBeneficiaries;

Route::get('/', function () {
    return redirect()->route('dashboard');
});
//
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/export-supports/{record}', [ReportController::class, 'exportSupports'])->name('export.supports');

Route::get('/export-transactions/{record}', [ReportController::class, 'exportTransactions'])

->name('export.transactions');

Route::get('/export-beneficiaries/{distribution}/{filter}', [ReportController::class, 'exportBeneficiaries'])
    ->name('export.beneficiaries');

    Route::get('/export-crops/{distribution}', [ReportController::class, 'exportCrops'])
    ->name('export.crops');

    // Barangay-specific export routes
    Route::get('/export-barangay-transactions/{record}', [ReportController::class, 'exportBarangayTransactions'])
    ->name('export.barangay.transactions');

    Route::get('/export-barangay-beneficiaries/{barangayDistribution}/{filter}', [ReportController::class, 'exportBarangayBeneficiaries'])
    ->name('export.barangay.beneficiaries');

    Route::get('/export-barangay-crops/{barangayDistribution}', [ReportController::class, 'exportBarangayCrops'])
    ->name('export.barangay.crops');


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
                // Direct access to staff dashboard
                return redirect()->route('staff.dashboard');
                break;
            default:
                return view('dashboard');
                break;
        }

    })->name('dashboard');


    // Removed support code middleware - direct access for members
    Route::get('/member-dashboard', MemberDashboard::class)->name('member.dashboard');
    Route::get('/scan-qr', QrScannerPage::class)->name('qr-scan');

    // Keep these routes for backward compatibility
    Route::get('/support/dashboard', function () {
        return redirect()->route('member.dashboard');
    })->name('support.dashboard');


    Route::get('/support-not-authorize', SupportNotAuthorize::class)->name('support-not-authorize');

    // Staff routes with proper namespace references
    Route::get('/staff/dashboard', App\Livewire\Staff\StaffDashboard::class)->name('staff.dashboard');
    Route::get('/staff/transactions', App\Livewire\Staff\TransactionList::class)->name('staff.transactions');
    Route::get('/staff/beneficiaries/{distribution?}', App\Livewire\Staff\BeneficiaryList::class)->name('staff.beneficiaries');
    Route::get('/staff/distributions', App\Livewire\Staff\BarangayDistributionList::class)->name('staff.distributions');
    Route::get('/staff/distributions/{distribution}', App\Livewire\Staff\DistributionDetails::class)->name('staff.distribution.details');
    Route::get('/staff/qr-scanner', App\Livewire\Staff\QrScannerPage::class)->name('staff.qr-scanner');






});

Route::get('/test-qr-mail', function () {

    $beneficiary = Beneficiary::with(['distributionItem.distribution','distributionItem.item'])->latest()->first();
    // dd($beneficiary);
    return view('emails.qrmail',['beneficiary'=>$beneficiary,'distribution'=> $beneficiary->distributionItem->distribution]);
    // try {
    //     $beneficiary = Beneficiary::with(['distributionItem.distribution', 'distributionItem.item'])->first();

    //     if (!$beneficiary) {
    //         return "No beneficiary found.";
    //     }

    //     Mail::to($beneficiary->email)->send(new QrMail($beneficiary));

    //     return "QR email sent to {$beneficiary->email}";
    // } catch (\Exception $e) {
    //     // Log the error for debugging
    //     Log::error('Error sending QR email: ' . $e->getMessage());

    //     return "Failed to send email. Check the logs for details.";
    // }
});
