<?php

    namespace Modules\Resilience\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Database\Eloquent\Model;
    use Modules\Resilience\Database\Seeders\ConsultationQuestionTypesTableSeeder;

    class ResilienceDatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            Model::unguard();
            $this->call([
                ConsultationQuestionTypesTableSeeder::class,
                SeedSettingTableTableSeeder::class,
                SeedClassAndPositionsTablesSeederTableSeeder::class,
            ]);
        }
    }
