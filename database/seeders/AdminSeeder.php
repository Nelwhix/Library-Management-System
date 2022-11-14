<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $youthAccessLevelId = AccessLevel::where('name', 'Youth')->pluck('id')->first();

        // creating an author
        User::factory()->create([
            'firstName' => 'Wole',
            'lastName' => 'Soyinka',
            'userName' => 'Wolibobo',
            'email' => 'wolibobo@gmail.com',
            'password' => Hash::make('wolibobo'), // password
            'access_level_id' => $youthAccessLevelId
        ])->assignRole('author');

        // creating an admin
        User::factory()->create([
            'firstName' => 'check',
            'lastName' => 'dc',
            'userName' => 'check-dc',
            'email' => 'chech-dc@gmail.com',
            'password' => Hash::make('check'), // password
            'access_level_id' => $youthAccessLevelId
        ])->assignRole('admin');

        // creating a super user
        User::factory()->create([
            'firstName' => 'Nelson',
            'lastName' => 'Isioma',
            'userName' => 'Nelwhix',
            'email' => 'nelsonisioma1@gmail.com',
            'password' => Hash::make('admin123'), // password
            'access_level_id' => $youthAccessLevelId
        ])->syncRoles(['author', 'reader', 'admin']);
    }
}
