<?php

namespace Modules\Cocktail\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CocktailDatabaseSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();
        $this->call([
            CocktailSettingTableSeederTableSeeder::class,
        ]);
    }
}
