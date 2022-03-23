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
}