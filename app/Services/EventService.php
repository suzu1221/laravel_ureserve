<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventService
{
  // イベント登録時に指定した時間が予約済みか確認する
  public static function checkEventDuplication($eventDate,$startTime,$endTime)
  {
    $check = DB::table('events')
    ->whereDate('start_date', $eventDate) // 日にち
    ->whereTime('end_date' , '>' ,$startTime)
    ->whereTime('start_date', '<', $endTime)
    ->exists(); // 存在確認

    return $check;
  }

    // イベント更新用の予約時間重複チェックメソッド
    // checkEventDuplicationとの違い
    // イベント更新で「checkEventDuplication」を使用してしまうと
    // 「同時間帯に予約済みのレコードが存在する（自分自身）」判定になり更新不可となる為
    // チェック方法を「同時間帯重複レコードが1件のみであれば更新OK」とする
    public static function countEventDuplication($eventDate,$startTime,$endTime)
    {
      return DB::table('events')
          ->whereDate('start_date', $eventDate) // 日にち
          ->whereTime('end_date' , '>' ,$startTime)
          ->whereTime('start_date', '<', $endTime)
          ->count(); // カウントで存在確認
    }

  // イベントの日付、開始時間、終了時間の連結に使用
  public static function joinDateAndTime($date,$time)
  {
    $join = $date . " " . $time;
    $dateTime = Carbon::createFromFormat('Y-m-d H:i',$join);

    return $dateTime;
  }

  // 7日分のイベントを取得するメソッド
  // $startDate：開始日
  // $endDate：終了日
  public static function getWeekEvents($startDate,$endDate)
  {
    // 中間テーブル（reservations）からイベントIDと予約人数を取得
    $reservedPeople = DB::table('reservations')
    ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
    ->whereNull('canceled_date')
    ->groupBy('event_id');

    // 上記クエリ（$reservedPeople）を使用してイベントID毎のイベントを取得する
    return DB::table('events')
    // joinSub（内部結合）を使用すると、number_of_people（予約人数）がnullのイベントが
    // 取得されずにindexに表示されないのでnullでも取得可能なieftjoinSub（外部結合）を使用する
    ->leftjoinSub($reservedPeople,'reservedPeople',function($join){
        $join->on('events.id','=','reservedPeople.event_id');
    })
    // whereBetween…カラムの値が指定した2つの値の間にある条件を抽出
    ->whereBetween('start_date',[$startDate,$endDate])
    ->orderBy('start_date','asc')
    ->get();
    
  }
  /**
   * イベントが満員かどうかチェックする
   *
   * @param  $event　イベントのコレクション
   * @return $reservablePeople 0が返れば満員、1以上なら予約に空きあり
   */
  // public static function getFullMemberCheck($event)
  // {
  //       // 中間テーブル（reservations）からイベントIDと予約人数を取得
  //       $reservedPeople = DB::table('reservations')
  //     ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
  //     ->whereNull('canceled_date')
  //     ->groupBy('event_id')
  //     // 選択したイベントである事を条件
  //     ->having('event_id', $event->id)
  //     ->first();

  //       // イベントが予約済みであれば計算
  //       if (!is_null($reservedPeople)) {
  //           // 予約上限の人数から予約済みの人数を引いて予約可能な人数を取得
  //           $reservablePeople = $event->max_people - $reservedPeople->number_of_people;
  //       }
  //       // 予約されてないイベントであれば最大人数がそのまま予約可能人数となる
  //       else {
  //           $reservablePeople = $event->max_people;
  //       }

  //       // dd($reservablePeople);

  //       return $reservablePeople;
  // }
    public static function getFullMemberCheck($events,$checkDay)
    {
      // 予約人数満員イベントの時間返却用配列
      $eventFullTime = [];

      // 選択した日付から7日の間に開催されるイベント分ループ
      foreach ($events as $event) {
        // チェック対象となる日付をCarbon形式にパース
        $checkDayCopy = \Carbon\Carbon::parse($checkDay);

        // イベントが予約済みであれば予約可能人数を計算
        if (!is_null($event->number_of_people)) {
          // 予約上限の人数から予約済みの人数を引いて予約可能な人数を取得
          $reservablePeople = $event->max_people - $event->number_of_people;
        }
        // 予約されてないイベントであれば最大人数がそのまま予約可能人数となる
        else {
          $reservablePeople = $event->max_people;
        }

        // $reservablePeopleが0なら満員=その時間を配列に格納する
        if ($reservablePeople == 0) {
          // for7回回して7日分チェック
          for ($i = 0; $i < 7; $i++) {
            // 10:00~20:00まで30分区切りで確認
            for ($j = 0; $j < 21; $j++) {
              // 定数で設定した時間を「:」区切りで時、分、秒に分割
              $ex_EventTime = explode(":",\Constant::EVENT_TIME[$j]);
              
              // 予約人数が満員になっているイベントの開始時間（Carbon形式にパース）を30分間隔で一致しているか判断
              if (\Carbon\Carbon::parse($event->start_date) == $checkDayCopy->setTime($ex_EventTime[0],$ex_EventTime[1],$ex_EventTime[2])) {
                // 満員となっているイベントの開始時間を格納
                array_push($eventFullTime, [
                  'eventFullTime'=> \Carbon\CarbonImmutable::parse($event->start_date),
                ]);
              }
            }
            // チェック日付を1日加算して次のループへ
            $checkDayCopy = \Carbon\CarbonImmutable::parse($checkDayCopy)->addDays(1);
          }
      }
    }
    return $eventFullTime;
   }
}