<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\SupportRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupportRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::all(); // Fetch all barangays

        if ($barangays->isEmpty()) {
            $this->command->info('No barangays found. Please seed barangays first.');
            return;
        }

        $roles = [
            ['name' => 'Scanner', 'description' => 'Responsible for scanning items and confirming claims.'],
            ['name' => 'Checker', 'description' => 'Checks beneficiary eligibility on the list.'],
            ['name' => 'Registrar', 'description' => 'Registers new beneficiaries in the system.'],
            ['name' => 'Distributor', 'description' => 'Handles physical distribution of items to beneficiaries.'],
            ['name' => 'Concern Support', 'description' => 'Handles beneficiary concerns, such as registration issues.'],
        ];

        foreach ($roles as $role) {
            SupportRole::create([
                'name' => $role['name'],
                'description' => $role['description'],
                'barangay_id' => $barangays->random()->id, // Assign a random barangay_id
                'is_active' => true,
            ]);
        }
    }
}
