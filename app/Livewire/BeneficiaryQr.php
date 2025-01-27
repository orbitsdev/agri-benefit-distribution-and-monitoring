<?php

namespace App\Livewire;

use DNS2D;
use Livewire\Component;
use App\Models\Beneficiary;

class BeneficiaryQr extends Component
{   

    public $record;

    public function mount(Beneficiary $beneficiary)
    {
        $this->record = $beneficiary;
    }

    public function render()
    {
        return view('livewire.beneficiary-qr', [
            'record' => $this->record,
        ]);
    }

   
}
