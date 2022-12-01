<?php
    
    use Illuminate\Database\Seeder;
    use Modules\Qualification\Entities\QualificationReminder;
    
    class QualificationReminderSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $data = [
                ['section_id' => 1, 'reminder_time' => 0, 'comment' => ''],
                ['section_id' => 2, 'reminder_time' => 0, 'comment' => ''],
                ['section_id' => 3, 'reminder_time' => 0, 'comment' => ''],
                ['section_id' => 4, 'reminder_time' => 0, 'comment' => ''],
                ['section_id'    => 5, 'reminder_time' => 0, 'comment' => 'This is For Weekly ReminderSection so we consider the json field',
                 'week_reminder' => '{"sec_reminder":0,"exp_reminder":0,"ref_reminder":0}',
                ],
            ];
            foreach ($data as $key => $value) {
                $data = QualificationReminder::updateOrCreate(['section_id' => $value['section_id']], $value);
            }
        }
    }
