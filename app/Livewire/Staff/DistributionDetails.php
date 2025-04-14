<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use App\Models\BarangayDistribution;
use Illuminate\Support\Facades\Auth;
use WireUi\Traits\WireUiActions;

class DistributionDetails extends Component
{
    use WireUiActions;

    public $distribution;
    public $record;
    public $progressData = [];

    public function mount($distribution)
    {
        $this->distribution = $distribution;
        $this->loadDistribution();
    }

    protected function loadDistribution()
    {
        $this->record = BarangayDistribution::with('distribution')
            ->where('id', $this->distribution)
            ->where('barangay_id', Auth::user()->barangay_id)
            ->first();

        if (!$this->record) {
            $this->notification()->error('Error', 'Distribution not found or you do not have access to it.');
            return redirect()->route('staff.dashboard');
        }

        // Calculate progress data for the view
        $totalBeneficiaries = $this->record->beneficiaries()->count();
        $claimedBeneficiaries = $this->record->beneficiaries()
            ->whereHas('cropsToReceive', function ($query) {
                $query->where('is_claimed', true);
            })
            ->count();

        $this->progressData = [
            'total' => $totalBeneficiaries,
            'claimed' => $claimedBeneficiaries,
            'remaining' => $totalBeneficiaries - $claimedBeneficiaries,
        ];
    }

    public function render()
    {
        return view('livewire.staff.distribution-details');
    }
}
