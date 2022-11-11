<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seedData = [
          [
              "name" => "Free",
              "duration" => "forever",
              "price" => 0,
          ],
            [
                "name" => "Silver",
                "duration" => "30",
                "price" => "10000"
            ],
            [
                "name" => "Bronze",
                "duration" => "30",
                "price" => "50000",
            ],
            [
                "name" => "Gold",
                "duration" => "30",
                "price" => "100000"
            ],
        ];

        foreach ($seedData as $seed) {
            Plan::create([
                'name' => $seed["name"],
                'price' => $seed["price"],
                'duration' => $seed["duration"]
            ]);
        }
    }
}
