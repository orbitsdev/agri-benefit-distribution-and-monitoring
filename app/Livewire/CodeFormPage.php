<?php

namespace App\Livewire;

use App\Models\Support;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Livewire\Component;

class CodeFormPage extends Component
{
    public string $code = '';

    public function submitCode()
    {
        // Validate input
        if (empty($this->code)) {
            Notification::make()
                ->title('Code is required')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();

        // Check if the code exists in the supports table for the same barangay
        $support = Support::where('unique_code', $this->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->first();

        if ($support) {
            // Save the code to the user's record
            $user->update(['code' => $this->code]);

            // Success notification
            Notification::make()
                ->title('Login Successful')
                ->success()
                ->send();

            return redirect()->route('member.dashboard');
        }

        // If the code is invalid, show an error notification
        Notification::make()
            ->title('Invalid Code')
            ->danger()
            ->send();
    }

    public function render()
    {
        return view('livewire.code-form-page');
    }
}
