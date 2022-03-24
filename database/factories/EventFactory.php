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
    // 10時～20時の範囲で30分単位のダミーデータ作成
    public function definition()
    {
        $availableHour = $this->faker->numberBetween(10, 18); //10時～18時
        $minutes = [0, 30]; // 00分か30分
        $mKey = array_rand($minutes); //00分か30分かランダムにキーを取得
        $addHour = $this->faker->numberBetween(1, 3); // イベント時間 1時間～3時間
        // 開始時間、終了時間の初期値（設定時刻に差異がありすぎても問題なので1ヶ月範囲で指定）
        $dammyDate = $this->faker->dateTimeThisMonth;

        // 今月の日付にsetTime(時,分)を設定して開始時間格納
        $startDate = $dammyDate->setTime($availableHour,$minutes[$mKey]);
        // 終了時間格納時、$startDateを使いまわしてmodefy（〇時間後）とすると
        // ミュータブル（変動）の関係で$startDateも（〇時間後）の値に変動してしまう
        // ので、cloneでコピーさせた後に終了時間格納
        $clone = clone $startDate;
        // .による文字列連結で開始時間から1~3時間後の時間を終了時間とする
        $endDate = $clone->modify('+'.$addHour.'hour');

        // dd($startDate,$endDate);

        return [
            'name' => $this->faker->name,
            'information' => $this->faker->realText,
            'max_people' => $this->faker->numberBetween(1,20),
            'start_date' => $startDate,
            'end_date' => $endDate,
            // 'start_date' => $dammyDate->format('Y-m-d H:i:s'),
            // 開始時刻より後ろで設定する必要があるので+1時間設定
            // 'end_date' => $dammyDate->modify('+1hour')->format('Y-m-d H:i:s'),
            'is_visible' => $this->faker->boolean,
        ];
    }
}
