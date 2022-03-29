<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MyPageService
{
  /**
   * ログイン済みユーザが予約したイベントの取得
   * ※キャンセル済みのイベントは取得対象外とする
   *
   * @param [type] $events　ログインしているユーザの全イベント（コレクション型）
   * @param [type] $string　取得対象のイベント種別
   *    'fromToday'…今日以降のイベント取得
   *    'past'…過去のイベント取得
   * @return $reservedEvents　引数に応じたイベントを配列にして返す
   */
  public static function reservedEvent($events,$string)
  {
      $reservedEvents = [];

      if($string === 'fromToday')
      {
					// 昇順（今日以降のイベントで並び替え）で1件ずつ格納
          foreach ($events->sortBy('start_date') as $event) {
						// キャンセルされていない かつ イベント開始日が現在日付以降であれば
						if(is_null($event->pivot->canceled_date) &&
						$event->start_date >= Carbon::now()->format('Y-m-d 00:00:00'))
						{
							$eventInfo = [
								'id' => $event->id,
								'name' => $event->name,
								'start_date' => $event->start_date,
								'end_date' => $event->end_date,
								'number_of_people' => $event->pivot->number_of_people,
							];

							array_push($reservedEvents,$eventInfo);
						}
          }
      }

      if($string === 'past')
      {
				// 降順（今日以前のイベントで並び替え）で1件ずつ格納
        foreach ($events->sortByDesc('start_date') as $event) {
            // キャンセルされていない かつ イベント開始日が現在日付以前であれば
						if(is_null($event->pivot->canceled_date) &&
						$event->start_date < Carbon::now()->format('Y-m-d 00:00:00'))
						{
							$eventInfo = [
								'id' => $event->id,
								'name' => $event->name,
								'start_date' => $event->start_date,
								'end_date' => $event->end_date,
								'number_of_people' => $event->pivot->number_of_people,
							];

							array_push($reservedEvents,$eventInfo);
						}
        }
      }

      return $reservedEvents;
  }
}