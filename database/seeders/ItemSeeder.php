<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = Barangay::all(); // Fetch all barangays

        $items = [
            ['name' => 'Rice Seeds', 'type' => 'Product'],
            ['name' => 'Corn Seeds', 'type' => 'Product'],
            ['name' => 'Fertilizer (Organic)', 'type' => 'Product'],
            ['name' => 'Fertilizer (Inorganic)', 'type' => 'Product'],
            ['name' => 'Tractor Usage Voucher', 'type' => 'Voucher'],
            ['name' => 'Irrigation Assistance', 'type' => 'Service'],
            ['name' => 'Cash Assistance', 'type' => 'Money'],
            ['name' => 'Pesticides', 'type' => 'Product'],
            ['name' => 'Water Pumps', 'type' => 'Equipment'],
            ['name' => 'Training on Modern Farming', 'type' => 'Service'],
            ['name' => 'Small Farm Implements', 'type' => 'Equipment'],
            ['name' => 'Animal Feed', 'type' => 'Product'],
            ['name' => 'Livestock', 'type' => 'Product'],
        ];

        foreach ($barangays as $barangay) {
            foreach ($items as $item) {
            DB::table('items')->insert([
                'name' => $item['name'],
                'type' => $item['type'],
                'barangay_id' => $barangay->id,
            ]);
            }
        }
    
    
    }
}
