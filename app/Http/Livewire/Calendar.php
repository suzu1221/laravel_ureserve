<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\CarbonImmutable; // Immutable（イミュータブル 不変）のCarbonを使用
use App\Services\EventService;

class Calendar extends Component
{
    public $currentDate; // 本日の日付格納
    public $currentWeek; // 本日から7日間の日付格納
    public $day; // currentWeek格納用
    public $checkDay; // 判定用日付格納
    public $dayOfWeek; // 判定用曜日格納
    public $sevenDaysLater; // 本日から7日後の日付格納
    public $events; // 本日から7日間に開催されるイベント格納

    public $fullMemberCheck; // イベント満員確認用

    // mount()…コンストラクタ的なメソッド
    // 本日から7日分の計算を実施して配列に格納
    public function mount()
    {
        $this->currentDate = CarbonImmutable::today();
        $this->sevenDaysLater = $this->currentDate->addDays(7);
        $this->currentWeek = [];

        // 本日から7日後までのイベント取得
        // ※値を渡す際にはformatで整形
        $this->events = EventService::getWeekEvents(
            $this->currentDate->format('Y-m-d'),
            $this->sevenDaysLater->format('Y-m-d')
        );

        // dd($this->events);

        // イベントが満員かどうかチェック
        if($this->events->isNotEmpty()){
            $this->fullMemberCheck = EventService::getFullMemberCheck($this->events);
        }

        // dd($this->fullMemberCheck);
        
        for ($i=0; $i < 7; $i++) { 
            // addDaysでtoday（今日）からインクリメント分の日数を加算
            $this->day = CarbonImmutable::today()->addDays($i)->format('m月d日');
            $this->checkDay = CarbonImmutable::today()->addDays($i)->format('Y-m-d');
            // 曜日取得（dayNameはCarbonの機能）
            $this->dayOfWeek = CarbonImmutable::today()->addDays($i)->dayName;

            // 連想配列で格納（日付,判定用日付,判定用曜日）
            // 格納例
            // "day" => "03月24日"
            // "checkDay" => "2022-03-24"
            // "dayOfWeek" => "木曜日"
            array_push($this->currentWeek,[
                'day' => $this->day,
                'checkDay' => $this->checkDay,
                'dayOfWeek' => $this->dayOfWeek,
                ]
            );
        }

        // dd($this->currentWeek);
    }

    // flatpickrで選択した日付から7日後の日付取得
    public function getDate($date)
    {
        $this->currentDate = $date; // 文字列
        // 文字列からCarbonインスタンスに整形してからaddDays
        $this->sevenDaysLater = CarbonImmutable::parse($this->currentDate)->addDays(7);
        $this->currentWeek = [];

        // 選択した日付から7日後までのイベント取得
        // ※値を渡す際にはformatで整形
        $this->events = EventService::getWeekEvents(
            $this->currentDate,
            $this->sevenDaysLater->format('Y-m-d')
        );

        // イベントが満員かどうかチェック
        if($this->events->isNotEmpty()){
            $this->fullMemberCheck = EventService::getFullMemberCheck($this->events);
        }


        for ($i=0; $i < 7; $i++) { 
            // 文字列形式をparsonでCarbonインスタンスに整形
            // addDaysでインクリメント分の日数を加算
            $this->day = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('m月d日');
            $this->checkDay = CarbonImmutable::parse($this->currentDate)->addDays($i)->format('Y-m-d');
            $this->dayOfWeek = CarbonImmutable::parse($this->currentDate)->addDays($i)->dayName;

            array_push($this->currentWeek,[
                'day' => $this->day,
                'checkDay' => $this->checkDay,
                'dayOfWeek' => $this->dayOfWeek,
                ]
            );
        }
    }

    public function render()
    {
        return view('livewire.calendar');
    }
}
