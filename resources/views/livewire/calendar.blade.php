<div>
    <div class="text-center text-sm">
        日付を選択してください。本日から最大30日先まで選択可能です。
    </div>
    <input id="calendar" class="block mt-1 mx-auto mb-2" 
    type="text" name="calendar"
    value="{{ $currentDate }}"
    wire:change="getDate($event.target.value)"/>

    <div class="flex mx-auto">
        <x-calendar-time />
        @for ($i = 0; $i < 7; $i++)
            <div class="w-32">
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['day'] }}</div>
                <div class="py-1 px-2 border border-gray-200 text-center">{{ $currentWeek[$i]['dayOfWeek'] }}</div>
                @for ($j = 0; $j < 21; $j++)
                    @if ($events->isNotEmpty())
                        {{--  定数で設定した30分ごとにイベントの開始時間をチェック　ヒットしたら該当イベントを表示  --}}
                        @if (!is_null($events->firstWhere('start_date',$currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j])))
                            @php
                                /*イベントID*/
                                $eventId = $events->firstWhere('start_date',$currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j])->id;
                                /*イベント名*/
                                $eventName = $events->firstWhere('start_date',$currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j])->name;
                                
                                $eventInfo = $events->firstWhere('start_date',$currentWeek[$i]['checkDay'] . " " . \Constant::EVENT_TIME[$j]);
                                /*  背景色の変更対象となる数を取得
                                    　開始時間と終了時間の差分計算（diffInMinutesで分単位の差分を確認、30で割り、既に背景色を変更している項目分-1する）  
                                    　例：開始時間10：00 終了時間11：00の場合　60分/30-1で1が格納される */
                                $eventPeriod = \Carbon\Carbon::parse($eventInfo->start_date)->diffInMinutes($eventInfo->end_date) / 30 - 1; // 差分
                            @endphp
                            <a href="{{ route('events.detail',['id' => $eventId]) }}">
                                {{--  予約が満員でなければ背景色青  --}}
                                @if ($fullMemberCheck > 0)
                                    <div class="py-1 px-2 h-8 border border-gray-200 text-xs bg-blue-100">
                                        {{ $eventName }}
                                    </div>
                                @else
                                    <div class="py-1 px-2 h-8 border border-gray-200 text-xs bg-red-100">
                                        {{ $eventName }}
                                    </div>
                                @endif
                            
                                {{--  $eventPeriodが0より多い = イベント予約時間が1時間以上であれば  --}}
                                @if ($eventPeriod > 0)
                                    @for ($k = 0; $k < $eventPeriod; $k++)
                                        {{--  背景色を変更する  --}}
                                        @if ($fullMemberCheck > 0)
                                            <div class="py-1 px-2 h-8 border border-gray-200 bg-blue-100"></div>
                                        @else
                                            <div class="py-1 px-2 h-8 border border-gray-200 bg-red-100"></div>
                                        @endif
                                    @endfor
                                    {{--  背景色を変更した数だけforインクリメント用変数を進める  --}}
                                    @php $j += $eventPeriod @endphp
                                @endif
                            </a>
                        @else
                            <div class="py-1 px-2 h-8 border border-gray-200"></div>
                        @endif
                    @else
                        <div class="py-1 px-2 h-8 border border-gray-200"></div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>
</div>
