<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ulid\Ulid;

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
            DB::table('status')->insert([
                "id" => Ulid::generate(),
                "name" => $seed["name"],
                "description" => $seed["description"],
                "statusable_id" => Ulid::generate(),
                "statusable_type" => fake()->randomElement(["App\Models\Book", "App\Models\User", "App\Models\Plan"]),
                "created_at" => now()
            ]);
        }
    }
}
