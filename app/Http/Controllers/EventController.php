<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\EventService;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::today();
        
        // 中間テーブル（reservations）からイベントIDと予約人数を取得
        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');

        // dd($reservedPeople);

        // 上記クエリ（$reservedPeople）を使用してイベントID毎のイベントを取得する
        $events = DB::table('events')
        // joinSub（内部結合）を使用すると、number_of_people（予約人数）がnullのイベントが
        // 取得されずにindexに表示されないのでnullでも取得可能なieftjoinSub（外部結合）を使用する
        ->leftjoinSub($reservedPeople,'reservedPeople',function($join){
            $join->on('events.id','=','reservedPeople.event_id');
        })
        ->whereDate('start_date','>=',$today)
        ->orderBy('start_date','asc')
        ->paginate(10);

        return view('manager.events.index',
        compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manager.events.create');
    }

    public function store(StoreEventRequest $request)
    {
        // 入力した予約時間が既に登録されているか確認
        $check = EventService::checkEventDuplication(
            $request['event_date'],
            $request['start_time'],
            $request['end_time']
        );

        if($check){
            session()->flash('status','この時間帯は既に他の予約が存在します。');
            return view('manager.events.create');
        }

        // dd($request);

        // DB格納前にフォーマット形成（イベント日付と開始時間の連結後、格納用にフォーマット形成）
        $startDate = EventService::joinDateAndTime(
            $request['event_date'],
            $request['start_time'],
        );

        // 終了時間にも同対応
        $endDate = EventService::joinDateAndTime(
            $request['event_date'],
            $request['end_time'],
        );

        Event::create([
            'name' => $request['event_name'],
            'information' => $request['information'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_people' => $request['max_people'],
            'is_visible' => $request['is_visible'],
        ]);

        session()->flash('status','登録OKです');
        return to_route('events.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        $event = Event::findOrFail($event->id);

        // $eventに紐づくUserをリレーションで取得
        $users = $event->users;

        // 予約情報格納用変数
        $reservations = [];

        // イベント予約情報の格納（イベントを予約したユーザ名、予約人数、キャンセル日）
        // $eventに紐づくUserを1件ずつ連想配列「$reservedInfo」に格納
        foreach($users as $user)
        {
            $reservedInfo = [
                'name' => $user->name,
                // 中間テーブルから取得したカラムはpivot経由で取得
                'number_of_people' => $user->pivot->number_of_people,
                'canceled_date' => $user->pivot->canceled_date,
            ];
            // 予約情報格納用変数に1件ずつ格納
            array_push($reservations,$reservedInfo);
        }

        // dd($reservations);

        // Eventモデルで実装したアクセサの取得
        $eventDate = $event->eventDate;
        $startTime = $event->startTime;
        $endTime = $event->endTime;

        // dd($eventDate,$startTime,$endTime);

        return view('manager.events.show',
        compact('event','users','reservations','eventDate','startTime','endTime'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        $event = Event::findOrFail($event->id);

        // Eventモデルで実装したアクセサの取得
        $eventDate = $event->editEventDate;
        $startTime = $event->startTime;
        $endTime = $event->endTime;

        return view('manager.events.edit',
        compact('event','eventDate','startTime','endTime'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        // 入力した予約時間が既に登録されているか確認
        // ※自分自身の予約は重複対象となる為、重複したレコードが1件であればOKとする
        $check = EventService::countEventDuplication(
            $request['event_date'],
            $request['start_time'],
            $request['end_time']
        );

        // 重複したレコードが1件より多いのであれば警告
        // ※editで使用する項目を設定してから遷移させる（editメソッドから引用）
        if($check > 1){
            $event = Event::findOrFail($event->id);

            $eventDate = $event->editEventDate;
            $startTime = $event->startTime;
            $endTime = $event->endTime;

            session()->flash('status','この時間帯は既に他の予約が存在します。');
            return view('manager.events.edit',
            compact('event','eventDate','startTime','endTime'));
        }

        // dd($request);

        // DB格納前にフォーマット形成（イベント日付と開始時間の連結後、格納用にフォーマット形成）
        $startDate = EventService::joinDateAndTime(
            $request['event_date'],
            $request['start_time'],
        );

        // 終了時間にも同対応
        $endDate = EventService::joinDateAndTime(
            $request['event_date'],
            $request['end_time'],
        );

        $event = Event::findOrFail($event->id);
        
        $event->name = $request['event_name'];
        $event->information = $request['information'];
        $event->start_date = $startDate;
        $event->end_date = $endDate;
        $event->max_people = $request['max_people'];
        $event->is_visible = $request['is_visible'];

        $event->save();

        session()->flash('status','更新しました。');
        return to_route('events.index');
    }

    // 今日以前のイベントを表示
    public function past()
    {
        $today = Carbon::today();

        $reservedPeople = DB::table('reservations')
        ->select('event_id', DB::raw('sum(number_of_people) as number_of_people'))
        ->whereNull('canceled_date')
        ->groupBy('event_id');

        $events = DB::table('events')
        ->leftjoinSub($reservedPeople,'reservedPeople',function($join){
            $join->on('events.id','=','reservedPeople.event_id');
        })
        ->whereDate('start_date','<',$today)
        ->orderBy('start_date','desc')
        ->paginate(10);

        return view('manager.events.past',
        compact('events'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
