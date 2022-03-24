<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    /* イベント詳細取得メソッド
    　　$id:イベントID
     */
    public function detail($id)
    {
        $event = Event::findOrFail($id);

        // 中間テーブル（reservations）からイベントIDと予約人数を取得
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id')
        // 選択したイベントである事を条件
        ->having('event_id',$event->id)
        ->first();

        // イベントが予約済みであれば計算
        if(!is_null($reservedPeople))
        {
            // 予約上限の人数から予約済みの人数を引いて予約可能な人数を取得
            $reservablePeople = $event->max_people - $reservedPeople->number_of_people;
        }
        // 予約されてないイベントであれば最大人数がそのまま予約可能人数となる
        else
        {
            $reservablePeople = $event->max_people;
        }

        return view('event-detail',
        compact('event','reservablePeople'));
    }

    /* イベント予約メソッド */
    public function reserve(Request $request)
    {
        $event = Event::findOrFail($request->id);

        // 中間テーブル（reservations）からイベントIDと予約人数を取得
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id')
        // 選択したイベントである事を条件
        ->having('event_id',$event->id)
        ->first();

        // 予約件数が存在しない、もしくは「予約済み人数+予約希望人数」が予約可能最大人数以下であれば予約可能
        if(is_null($reservedPeople) || 
        $event->max_people >= $reservedPeople->number_of_people + $request->reserved_people)
        {
            Reservation::create([
                'user_id' => Auth::id(),
                'event_id' => $request['id'],
                'number_of_people' => $request['reserved_people'],
            ]);

            session()->flash('status','予約が完了しました。');
            return to_route('dashboard');
        }
        // 予約できない
        else
        {
            session()->flash('status','この人数は予約出来ません。');
            return view('dashboard');
        }
    }
}
