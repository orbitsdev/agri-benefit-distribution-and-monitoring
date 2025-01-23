<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Super Admin',
        ]);

        // Assign Admin users to each barangay in Isulan
        $barangays = [
            'Bambad', 'Bual', 'Dorado', 'Impao', 'Kalawag I', 'Kalawag II', 'Kalawag III', 'Kenram',
            'Kiwal', 'Laguilayan', 'Mapantig', 'New Pangasinan', 'Sampao', 'Tayugo', 'Villamor',
            'Biatin', 'Dinaig'
        ];

        foreach ($barangays as $key => $barangayName) {
            $barangay = Barangay::where('name', $barangayName)->first();

            if ($barangay) {
                User::create([
                    'name' => 'Admin of ' . $barangay->name,
                    'email' => 'admin' . $key . '@gmail.com',
                    'password' => Hash::make('password'),
                    'role' => 'Admin',
                    'barangay_id' => $barangay->id,
                ]);
            }
        }

        // Create a generic Member user without a specific barangay
        User::create([
            'name' => 'Member User',
            'email' => 'member@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Member',
        ]);

    }
}
