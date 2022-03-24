<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use App\Models\User;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'information',
        'max_people',
        'start_date',
        'end_date',
        'is_visible',
    ];

    // Userテーブルとのリレーション
    protected function users() 
    {
        // belongsToMany(リレーション対象のモデルクラス,中間テーブル名)
        return $this->belongsToMany(User::class,'reservations')
        // withPivot（中間テーブルから取得したい情報を指定）
        ->withPivot('id','number_of_people','canceled_date');
    }


    // アクセサ（データ取得時に内容を加工する）実装
    // メソッド名():返り値の型指定

    // イベントの日付取得
    protected function eventDate(): Attribute
    {
        return new Attribute(
            // parseで日時形式の文字列からインスタンスを作成
            // $thisで当モデルからstart_dateを取得
            get: fn() => Carbon::parse($this->start_date)->format('Y年m月d日')
        );
    }

    // イベントの開始時間取得
    protected function startTime(): Attribute
    {
        return new Attribute(
            get: fn() => Carbon::parse($this->start_date)->format('H時i分')
        );
    }

    // イベントの終了時間取得
    protected function endTime(): Attribute
    {
        return new Attribute(
            get: fn() => Carbon::parse($this->end_date)->format('H時i分')
        );
    }

    // イベント日付編集用メソッド
    // 上記eventDate()でフォーマットした形式「format('Y年m月d日')」では
    // update時にカラム指定の型と異なる為、専用メソッドで対応する
    protected function editEventDate(): Attribute
    {
        return new Attribute(
            // datetime型に対応する形でフォーマット
            get: fn() => Carbon::parse($this->start_date)->format('Y-m-d')
        );
    }

}
