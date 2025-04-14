<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use Illuminate\Support\Str;
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
        // Super Admin (including Municipal Admins)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Super Admin',
        ]);

        User::create([
            'name' => 'Municipal Admin',
            'email' => 'municipal@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Super Admin',
        ]);

        // Official Isulan Barangays
        $barangays = [
            'Bambad',
            'Bual',
            'Dansuli',
            'Dapantis',
            'Datu Wasay',
            'Kenram',
            'Kalawag I',
            'Kalawag II',
            'Kalawag III',
            'Kalandagan',
            'Kolambog',
            'Laguilayan',
            'Mapantig',
            'New Pangasinan',
            'Sampao',
            'Tayugo',
            'Villamor',
        ];

        // Admins assigned per barangay
        foreach ($barangays as $barangayName) {
            $barangay = Barangay::where('name', $barangayName)->first();

            if ($barangay) {
                $slug = Str::slug($barangay->name);
                $email = $slug . '@gmail.com';

                User::create([
                    'name' => 'Admin of ' . $barangay->name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'Admin',
                    'barangay_id' => $barangay->id,
                ]);
            }
        }

        // Member or Verifier
        User::create([
            'name' => 'Verifier Staff',
            'email' => 'verifier@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Member',
        ]);
    }
}
