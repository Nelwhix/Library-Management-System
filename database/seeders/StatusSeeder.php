<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
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
              "name" => "borrowed",
              "description" => "borrowed entity"
          ],
          [
            "name" => "active",
            "description" => "active entity"
          ],
            [
            "name" => "inactive",
            "description" => "inactive entity"
            ],
            [
                "name" => "available",
                "description" => "available book"
            ],
        ];

        foreach ($seedData as $seed) {
            Status::create([
                "name" => $seed["name"],
                "description" => $seed["description"],
            ]);
        }
    }
}
