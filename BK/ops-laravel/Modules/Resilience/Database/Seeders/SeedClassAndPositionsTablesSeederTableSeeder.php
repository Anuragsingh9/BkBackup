<?php

    namespace Modules\Resilience\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Database\Eloquent\Model;
    use Modules\Resilience\Entities\ConsultationSignUpClass;
    use Modules\Resilience\Entities\ConsultationSignUpClassPosition;

    class SeedClassAndPositionsTablesSeederTableSeeder extends Seeder
    {


        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            Model::unguard();
            $label = config('resilience.default_class_label');
            $classInsert = [
                [
                    'label'         => config('resilience.default_class_name'),
                    'sort_order'    => 1,
                    'class_type'    => 1,
                    'label_setting' => $label,
                ],
                [
                    'label'         => config('resilience.default_class_name'),
                    'sort_order'    => 1,
                    'class_type'    => 2,
                    'label_setting' => $label,
                ],
            ];
            foreach ($classInsert as $item) {
                $class = ConsultationSignUpClass::updateOrCreate([
                    'label'      => $item['label'],
                    'class_type' => $item['class_type'],
                ],
                    $item);
                ConsultationSignUpClassPosition::updateOrCreate(
                    ['consultation_sign_up_class_uuid' => $class->uuid,
                    ], [
                        'consultation_sign_up_class_uuid' => $class->uuid,
                        'positions'                       => 'Regular',
                        'sort_order'                      => 1,
                    ]
                );
            }
        }

    }
