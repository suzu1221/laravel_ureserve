import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js"

// 第1引数でIDを命名
flatpickr("#event_date", {
  // 日本指定
  "locale": Japanese,
  // 今日以前の日付は指定不可
  minDate: "today",
  // 30日後まで指定可
  maxDate: new Date().fp_incr(30)
});

// 開始時間、終了時間用のオプション設定
const setting = {
  "locale": Japanese,
  enableTime:true,
  noCalendar:true,
  dateFormat:"H:i",
  time_24hr:true,
  minTime:"10:00",
  maxTime:"20:00",
}

flatpickr("#start_time", setting);
flatpickr("#end_time", setting);