<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\BarangayDistribution;
use Illuminate\Support\Facades\Auth;

class DistributionProgress extends Component
{
    public $distribution;
    public $record;
    public $progressData = [];
    
    public function mount($distribution)
    {
        $this->distribution = $distribution;
        $this->loadDistribution();
    }
    
    #[On('beneficiary-claimed')]
    #[On('beneficiary-unclaimed')]
    public function refreshProgress()
    {
        $this->loadDistribution();
    }
    
    protected function loadDistribution()
    {
        $this->record = BarangayDistribution::with('distribution')
            ->where('id', $this->distribution)
            ->where('barangay_id', Auth::user()->barangay_id)
            ->first();
            
        if (!$this->record) {
            return;
        }
        
        // Calculate progress data for the view
        $totalBeneficiaries = $this->record->beneficiaries()->count();
        
        // Count claimed beneficiaries - using the cropsToReceive relationship
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
        return view('livewire.staff.distribution-progress');
    }
}
