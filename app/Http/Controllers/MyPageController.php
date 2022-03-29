<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Event;
use App\Models\Reservation;
use App\Services\MyPageService;
use Carbon\Carbon;

class MyPageController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $events = $user->events;

        // ログインしたユーザのイベント情報取得
        // 引数「'fromToday'」で今日以降のイベント取得
        $fromTodayEvents = MyPageService::reservedEvent($events,'fromToday');
        // 引数「'past'」で今日以前のイベント取得
        $pastEvents = MyPageService::reservedEvent($events,'past');

        // dd($user,$events,$fromTodayEvents,$pastEvents);

        return view('mypage/index',
        compact('fromTodayEvents','pastEvents'));
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        $reservation = Reservation::where('user_id','=',Auth::id())
        ->where('event_id','=',$id)
        ->latest() // 引数無しだと「created_at」が新しい順
        ->first();

        return view('mypage.show',
        compact('event','reservation'));

    }

    public function cancel($id)
    {
        $reservation = Reservation::where('user_id','=',Auth::id())
        ->where('event_id','=',$id)
        ->latest() // 引数無しだと「created_at」が新しい順
        ->first();

        $reservation->canceled_date = Carbon::now()->format('Y-m-d H:i:s');
        $reservation->save();

        session()->flash('status','キャンセルが完了しました。');
        return to_route('dashboard');
    }

}
