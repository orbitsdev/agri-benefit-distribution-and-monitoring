<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;

class TransactionDetails extends Component
{
    public ?Transaction $record = null;

    public function mount(Transaction $record): void
    {
        $this->record = $record->load('beneficiary', 'distribution', 'distributionItem', 'barangay', 'support', 'media');
    }

    public function render()
    {
        return view('livewire.transaction-details');
    }
}
