<div style="text-align: center">
    {{--  // wire:click=“メソッド名”で実行  --}}
    <button wire:click="increment">+</button>

    {{--  // Counterクラス内プロパティを表示  --}}
    <h1>{{ $count }}</h1>
    <div class="mb-8"></div>

    こんにちは、{{ $name }}さん<br>
    {{--  <input type="text" wire:modeldebounce.2000ms="name">  --}}
    {{--  <input type="text" wire:model.lazy="name">  --}}
    {{--  <input type="text" wire:model.defer="name">  --}}
    <input type="text" wire:model="name">
    <br>
    <button wire:mouseover="mouseOver">マウスを合わせてね</button>
</div>