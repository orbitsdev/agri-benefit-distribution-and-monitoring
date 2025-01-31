<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SystemUsersExport implements FromView
{
    protected $barangayId;

    public function __construct($barangayId)
    {
        $this->barangayId = $barangayId;
    }

    public function view(): View
    {
        // Fetch users based on barangay
        $users = User::where('barangay_id', $this->barangayId)->isNotAdmin()->latest()->get();

        return view('exports.system-users', [
            'users' => $users,
        ]);
    }
}
