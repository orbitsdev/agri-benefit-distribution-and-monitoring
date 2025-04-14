<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            ['name' => 'Bambad', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Bual', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Dansuli', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Dapantis', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Datu Wasay', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kenram', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kalawag I', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kalawag II', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kalawag III', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kalandagan', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Kolambog', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Laguilayan', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Mapantig', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'New Pangasinan', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Sampao', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Tayugo', 'location' => 'Isulan, Sultan Kudarat'],
            ['name' => 'Villamor', 'location' => 'Isulan, Sultan Kudarat'],
        ];

        // Insert barangays into the table
        DB::table('barangays')->insert($barangays);
    }
}
