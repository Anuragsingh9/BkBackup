<?php

namespace Modules\SuperAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Modules\SuperAdmin\Entities\SuperAdminUser;

class FirstSuperAdminTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        // if there is no user as super admin then add the default super admin
        if (SuperAdminUser::count() == 0) {
            SuperAdminUser::create([
                'fname'    => 'Gourav',
                'lname'    => 'Verma',
                'email'    => 'gourav.verma@kct-technologies.com',
                'password' => Hash::make('gourav.verma@kct-technologies.com'),
            ]);
        }
    }
}
