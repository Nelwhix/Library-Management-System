<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminPermissions = [
            'plan_manage',
            'lending_manage',
            'user_manage',
            'book_manage',
            'access_level_manage'
        ];

        $readerPermissions = [
          'profile_edit',
            'book_borrow',
            'plan_add'
        ];

        foreach($adminPermissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }

        foreach($readerPermissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }


        $admin = Role::create(['name' => 'admin']);
        $reader = Role::create(['name' => 'reader']);
        $author = Role::create(['name' => 'author']);

        $admin->syncPermissions($adminPermissions);
        $reader->syncPermissions($readerPermissions);
        $author->givePermissionTo($adminPermissions[3]);

        $youthAccessLevelId = AccessLevel::where('name', 'Youth')->pluck('id')->first();

//        // creating an author
//        User::factory()->create([
//            'firstName' => 'Wole',
//            'lastName' => 'Soyinka',
//            'userName' => 'Wolibobo',
//            'email' => 'wolibobo@gmail.com',
//            'password' => Hash::make('wolibobo'), // password
//            'access_level_id' => $youthAccessLevelId
//        ])->assignRole('author');
//
//        // creating an admin
//        User::factory()->create([
//            'firstName' => 'check',
//            'lastName' => 'dc',
//            'userName' => 'check-dc',
//            'email' => 'chech-dc@gmail.com',
//            'password' => Hash::make('check'), // password
//            'access_level_id' => $youthAccessLevelId
//        ])->assignRole('admin');
//
//        // creating a super user
//        User::factory()->create([
//            'firstName' => 'Nelson',
//            'lastName' => 'Isioma',
//            'userName' => 'Nelwhix',
//            'email' => 'nelsonisioma1@gmail.com',
//            'password' => Hash::make('admin123'), // password
//            'access_level_id' => $youthAccessLevelId
//        ])->syncRoles(['author', 'reader', 'admin']);
    }
}
