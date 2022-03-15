<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // 開始時間、終了時間の初期値（設定時刻に差異がありすぎても問題なので今月指定）
        $dammyDate = $this->faker->dateTimeThisMonth;

        return [
            'name' => $this->faker->name,
            'information' => $this->faker->realText,
            'max_people' => $this->faker->numberBetween(1,20),
            'start_date' => $dammyDate->format('Y-m-d H:i:s'),
            // 開始時刻より後ろで設定する必要があるので+1時間設定
            'end_date' => $dammyDate->modify('+1hour')->format('Y-m-d H:i:s'),
            'is_visible' => $this->faker->boolean,
        ];
    }
}
