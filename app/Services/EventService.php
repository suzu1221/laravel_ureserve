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

  // イベントの日付、開始時間、終了時間の連結に使用
  public static function joinDateAndTime($date,$time)
  {
    $join = $date . " " . $time;
    $dateTime = Carbon::createFromFormat('Y-m-d H:i',$join);

    return $dateTime;
  }
}