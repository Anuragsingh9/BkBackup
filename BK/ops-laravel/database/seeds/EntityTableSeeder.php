<?php
    
    use Illuminate\Database\Seeder;
    use App\EntityType;
    
    class EntityTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $data = [
                ['id' => 1, 'level' => 1, 'parent' => '0', 'name' => 'Instances de lobbying'],
                ['id' => 2, 'level' => 1, 'parent' => '0', 'name' => 'Companies'],
                ['id' => 3, 'level' => 1, 'parent' => '0', 'name' => 'Unions'],
                ['id' => 4, 'level' => 1, 'parent' => '0', 'name' => 'Press'],
            ];
            foreach ($data as $key => $value) {
                EntityType::updateOrCreate(['id' => $value['id']], $value);
            }
        }
    }
