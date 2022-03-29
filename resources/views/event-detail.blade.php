<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          イベント詳細
      </h2>
  </x-slot>

  <div class="pt-4 pb-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="max-w-2xl py-4 mx-auto">
                <x-jet-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
        
                <form method="post" action="{{ route('events.reserve', ['id' => $event->id ]) }}">
                  @csrf
                    <div>
                        <x-jet-label for="event_name" value="イベント名" />
                        {{ $event->name }}
                    </div>
                    <div class="mt-4">
                        <x-jet-label for="information" value="イベント詳細" />
                        {{--  無対応でinformationを表示させると改行が含まれている場合に改行せず1行表示となる為、以下構文で対応  --}}
                        {{--  {{ $event->information }}  --}}
                        {!! nl2br(e($event->information)) !!}
                    </div>
        
                    <div class="md:flex justify-between"> 
                        <div class="mt-4">
                            <x-jet-label for="event_date" value="イベント日付" />
                            {{ $event->eventDate }}
                        </div>
                        <div class="mt-4">
                            <x-jet-label for="start_time" value="開始時間" />
                            {{ $event->startTime }}
                        </div>
                        <div class="mt-4">
                            <x-jet-label for="end_time" value="終了時間" />
                            {{ $event->endTime }}
                        </div>
                    </div>

                    <div class="md:flex justify-between items-end">
                        <div class="mt-4">
                            <x-jet-label for="max_people" value="定員数" />
                            {{ $event->max_people }}
                        </div>
                        <div class="mt-4">
                          {{--  予約が満員なら警告表示  --}}
                          @if ($reservablePeople <= 0)
                            <span class="text-red-500">このイベントは満員です</span>
                          @else
                            <x-jet-label for="reserved_people" value="予約人数" />
                            <select name="reserved_people">
                              {{--  予約可能人数分ループしてオプション設定  --}}
                              @for ($i = 1; $i <= $reservablePeople; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                              @endfor
                            </select>
                          @endif
                        </div>
                        {{--  重複予約でない　かつ　前回予約がキャンセル済みであれば予約ボタン表示  --}}
                        @if ($isReserved === null)
                          {{--  POST通信でIDを渡すのでhiddenを使用し裏で格納  --}}
                          <input type="hidden" name="id" value="{{ $event->id }}">
                          {{--  予約が満員でなければ予約するボタン表示  --}}
                          @if ($reservablePeople > 0)
                            <x-jet-button class="ml-4">予約する</x-jet-button>
                          @endif
                        {{--  既に予約済みのイベントだった場合  --}}
                        @else
                        <span class="text-xs">このイベントは既に予約済みです。</span>
                        @endif
                    </div>
                </form>
            </div>
          </div>
      </div>
  </div>
</x-app-layout>
