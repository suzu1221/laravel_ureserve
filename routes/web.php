<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivewireTestController;

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

Route::controller(LivewireTestController::class)
// prefixの後にnameで命名すると、各ルートの命名記載を省略できる
// 例、以下のようにprefixの後のnameで「livewire-test.」と命名すると
// Route::get('index','index')->name('livewire-test.index');
// と同義になる
->prefix('livewire-test')->name('livewire-test.')->group(function(){
    Route::get('index','index')->name('index');
    Route::get('register','register')->name('register');
});
