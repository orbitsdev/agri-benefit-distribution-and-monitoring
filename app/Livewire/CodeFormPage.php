<?php

namespace App\Livewire;

use App\Models\Support;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class CodeFormPage extends Component
{
    use WireUiActions;

    public string $code = '';

    public function submitCode()
    {
        // Validate Input
        $this->validate([
            'code' => 'required|min:3',
        ], [
            'code.required' => 'Code is required!',
            'code.min' => 'Code must be at least 3 characters.',
        ]);

        $user = Auth::user();

        // Check if the code exists in the `supports` table for the same barangay
        $support = Support::where('unique_code', $this->code)
            ->whereHas('distribution', function ($query) use ($user) {
                $query->where('barangay_id', $user->barangay_id);
            })
            ->first();

        if ($support) {
            // Save the valid code to the user's record
            $user->update(['code' => $this->code]);

            // Display Success Dialog (Using WireUI)
            $this->dialog()->success(
                title: 'Login Successful',
                description: 'You have successfully logged in!',
            );

            return redirect()->route('support.dashboard'); // Redirect to support dashboard
        } else {
            // Display Error Dialog (Using WireUI)
            $this->dialog()->error(
                title: 'Invalid Code',
                description: 'The code entered is not valid. Please try again.',
            );

            // Clear Input Field
            $this->reset('code');
        }
    }

    public function render()
    {
        return view('livewire.code-form-page');
    }
}
