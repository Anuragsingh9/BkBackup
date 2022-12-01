<?php

namespace Modules\UserManagement\Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\Permission;
use Modules\UserManagement\Entities\Role;

class RoleAndPermissionTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $roles = config('usermanagement.auth.roles');
        $permissions = config('usermanagement.auth.permissions');

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => $roles['super_admin']]);
        // giving permission to super admin
        $role->givePermissionTo([
            $permissions['check-default-zoom-webinar'],
            $permissions['update_content'],
        ]);
        Role::firstOrCreate(['name' => $roles['org_admin']]);
        Role::firstOrCreate(['name' => $roles['main_organiser']]);
        Role::firstOrCreate(['name' => $roles['group_organiser']]);
        Role::firstOrCreate(['name' => $roles['user']]);
        Role::firstOrCreate(['name' => $roles['executive']]);
        Role::firstOrCreate(['name' => $roles['manager']]);
        Role::firstOrCreate(['name' => $roles['employee']]);
        Role::firstOrCreate(['name' => $roles['other']]);
    }
}
