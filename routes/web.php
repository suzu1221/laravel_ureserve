<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivewireTestController;
use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// manager権限のルート設定
// perfixで以下のように設定するとURLが「http://127.0.0.1:8000/manager/index」のような構成になる
Route::prefix('manager')
// AuthServiceProvider.phpで設定した権限チェック
->middleware('can:manager-higher')
->group(function(){
    // ルーティングは上から処理される
    // リソースの下に書くと /past部分がパラメータと勘違いされるのでリソースの上に書く
    // ※ルートを複数記載する際にはリソースを一番下に持ってきた方が無難？
    Route::get('events/past', [EventController::class, 'past'])->name('events.past');
    Route::resource('events', EventController::class);
});

// user権限のルート設定
Route::middleware('can:user-higher')->group(function(){
    Route::get('index', function () {
    dd('user');
    });
});

Route::controller(LivewireTestController::class)
// prefixの後にnameで命名すると、各ルートの命名記載を省略できる
// 例、以下のようにprefixの後のnameで「livewire-test.」と命名すると
// Route::get('index','index')->name('livewire-test.index');
// と同義になる
->prefix('livewire-test')->name('livewire-test.')->group(function(){
    Route::get('index','index')->name('index');
    Route::get('register','register')->name('register');
});

Route::get('alpine-test/index',[AlpineTestController::class,'index']);
