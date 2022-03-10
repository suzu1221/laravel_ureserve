<html>
 <head>
   {{--  JavaScript取り込みの為@livewireStyles追加  --}}
  @livewireStyles
 </head>

 <body>
  livewire テスト
  <div>
    {{--  フラッシュメッセージ表示  --}}
    @if (session()->has('message'))
        <div class="">
            {{ session('message') }}
        </div>
    @endif
  </div>

  {{--  <livewire:counter /> でも同じ意味になる  --}}
  @livewire('counter');

   {{--  JavaScript取り込みの為@livewireScripts追加  --}}
  @livewireScripts
 </body>

</html>