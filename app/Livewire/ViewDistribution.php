<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Distribution;

class ViewDistribution extends Component
{

    public Distribution $record;
    public function render()
    {
        return view('livewire.view-distribution',[
            'record'=> $this->record
        ]);
    }
}
