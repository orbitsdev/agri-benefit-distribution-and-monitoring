<?php

namespace App\Livewire;

use Livewire\Component;

class CropToReceiveQr extends Component
{

    public $code;

    public function mount($code)
    {
        $this->code = $code;
    }



    public function render()
    {
        return view('livewire.crop-to-receive-qr');
    }
}
