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
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }

        foreach($readerPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }


        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $reader = Role::create(['name' => 'reader', 'guard_name' => 'api']);
        $author = Role::create(['name' => 'author', 'guard_name' => 'api']);

        $admin->syncPermissions($adminPermissions);
        $reader->syncPermissions($readerPermissions);
        $author->givePermissionTo($adminPermissions[3]);
    }
}
