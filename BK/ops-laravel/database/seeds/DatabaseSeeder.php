<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            $this->call(SettingTableSeeder::class);
     		$this->call(EntityTableSeeder::class);
            $this->call(ActivityStatusTableSeeder::class);
            $this->call(ActivityTypeTableSeeder::class);
            $this->call(LabelCustomizationTableSeeder::class);
            $this->call(SkillTabFormatTableSeeder::class);
            $this->call(ActivityOverdueAlert::class);
            $this->call(TypologyListSeeder::class);
            $this->call(listsTableAllContactSeeder::class);
            $this->call(defaultListSeeder::class);
            $this->call(QualificationReminderSeeder::class);
            $this->call(GuideSeeder::class);
            
    }
}
