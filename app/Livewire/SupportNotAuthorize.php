<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupportNotAuthorize extends Component
{
    public function removeCode()
    {
        $user = Auth::user();
        $user->update(['code' => null]); // Remove the assigned support code
        return redirect()->route('support-login')->with('success', 'You have been removed from the current support session.');
    }

    public function render()
    {
        return view('livewire.support-not-authorize');
    }
}
