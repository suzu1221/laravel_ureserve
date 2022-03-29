// flatpickrインポート文
import flatpickr from "flatpickr";
// 日本語指定
import { Japanese } from "flatpickr/dist/l10n/ja.js"

// 基本構文
// flatpickr(第1引数にID命名,{第2引数に様々なオプション指定});
// 汎用的なオプション指定は定数で持たせるのもあり（定数「setting」が例）
// 使用例は「create.blade.php」等参照

// 第1引数でIDを命名
flatpickr("#event_date", {
  // 日本指定
  "locale": Japanese,
  // 今日以前の日付は指定不可
  minDate: "today",
  // 30日後まで指定可
  maxDate: new Date().fp_incr(30)
});

// 第1引数でIDを命名
flatpickr("#calendar", {
  // 日本指定
  "locale": Japanese,
  // 今日以前の日付は指定不可
  minDate: "today",
  // 30日後まで指定可
  maxDate: new Date().fp_incr(30)
});

// 開始時間、終了時間のオプション設定
const setting = {
  "locale": Japanese,
  enableTime:true,
  noCalendar:true,
  dateFormat:"H:i",
  time_24hr:true,
  minTime:"10:00",
  maxTime:"20:00",
  minuteIncrement: 30,
}

// 開始時間、終了時間のIDと上記オプション内容を設定
flatpickr("#start_time", setting);
flatpickr("#end_time", setting);