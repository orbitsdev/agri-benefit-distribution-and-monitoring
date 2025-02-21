<?php

namespace App\Livewire\Distributions;

use Livewire\Component;
use App\Models\Beneficiary;

class DistributionProgress extends Component
{
    public $distributionId;
    public $progressData = [];
    public $progressColor;

    public function mount($distributionId)
    {
        $this->distributionId = $distributionId;
        $this->calculateProgress(); // Ensure we get the initial progress on mount
    }

    public function calculateProgress()
    {
        if (!$this->distributionId) {
            $this->progressData = [
                'total' => 0,
                'claimed' => 0,
                'remaining' => 0,
                'percentage' => 0,
            ];
            $this->progressColor = '#3498db'; // Default color
            return;
        }

        $total = Beneficiary::whereHas('distributionItem', function ($query) {
            $query->where('distribution_id', $this->distributionId);
        })->count();

        $claimed = Beneficiary::whereHas('distributionItem', function ($query) {
            $query->where('distribution_id', $this->distributionId);
        })->where('status', Beneficiary::CLAIMED)->count();

        $percentage = $total > 0 ? round(($claimed / $total) * 100, 2) : 0;

        $this->progressData = [
            'total' => $total,
            'claimed' => $claimed,
            'remaining' => max(0, $total - $claimed),
            'percentage' => $percentage,
        ];

        $this->progressColor = match (true) {
            $percentage == 100 => '#2ecc71', // Light Green (Completed)
            $percentage > 50 => '#27ae60',   // Green (>50% completed)
            $percentage > 25 => '#f39c12',   // Orange (>25% completed)
            default => '#3498db',            // Blue (Low Progress)
        };
    }

    public function render()
    {
        return view('livewire.distributions.distribution-progress');
    }
}
