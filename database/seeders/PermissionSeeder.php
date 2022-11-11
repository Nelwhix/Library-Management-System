<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
    }
}
