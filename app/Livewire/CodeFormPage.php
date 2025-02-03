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

        $this->validate([
            'code' => 'required',
        ], [
            'code.required' => 'Code is required!',
            'code.min' => 'Code must be at least 3 characters.',
        ]);

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
            $this->notification()->success(
                'Login Successful',
                'You have successfully logged in!'
            );

            return redirect()->route('member.dashboard');
        }


    }

    public function render()
    {
        return view('livewire.code-form-page');
    }
}
