<?php

namespace Modules\SuperAdmin\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SuperAdmin\Entities\UserTag;
use Modules\SuperAdmin\Entities\UserTagLocale;

class UserTagFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array {
        return [
            'tag_type' => $this->faker->numberBetween(1, 2),
            'status'   => 3,
        ];
    }

    public function configure(): UserTagFactory {
        return $this->afterCreating(function (UserTag $userTag) {
            $en = new UserTagLocale([
                'locale' => 'en',
                'value'  => "EN_{$this->faker->name}",
            ]);
            $fr = new UserTagLocale([
                'locale' => 'fr',
                'value'  => "FR_$en->value",
            ]);
            $userTag->locales()->save($en);
            $userTag->locales()->save($fr);
        });
    }
}

