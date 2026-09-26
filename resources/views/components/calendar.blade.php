<?php

use App\Models\Calendar;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Yasumi\Yasumi; // 祝日判定用

new class extends Component
{
    /* #[Url]を付けることで、対象月の情報を渡せるように設定している
    （URLの?month=2026-05と自動で同期）
    */
    #[Url]
    public $month;

    public $currentMonth;

    #[On('calendar-created')]
    public function refreshList()
    {
        // イベント受信時に自動的に再描画される
    }

    // 今月をデフォルトで開くよう設定
    public function mount()
    {
        // URLパラメータにmonthが指定されていればそれを使い、無ければ今月をセット
        if ($this->month) {
            $this->currentMonth = $this->month;
        } else {
            $this->currentMonth = Carbon::now()->format('Y-m');
        }

        // データが取得できているかログでテスト
        Log::info('Current Month: ' . $this->currentMonth);
    }

    // 今月の定義
    public function thisMonth()
    {
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    // 前月へ移動
    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->format('Y-m');
        $this->month = $this->currentMonth;
    }

    // 翌月へ移動
    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->format('Y-m');
        $this->month = $this->currentMonth;
    }

    // 今月に戻る
    public function resetTothisMonth()
    {
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    // お手伝い登録用のポップアップを開く。対象の日付文字列（Y-m-d）をパラメータで渡す
    public function openCreateView($date)
    {
        $this->dispatch('open-registration-chore', date: $date);
    }

    public function render()
    {
        $targetDate = Carbon::parse($this->currentMonth);
        $today = Carbon::today();
        $holidays = Yasumi::create('Japan', $targetDate->year, 'ja_JP');

        // 1日の曜日（0:日～6:土）と当月の総日数を計算
        $firstDayOfWeek = $targetDate->copy()->firstOfMonth()->dayOfWeek;
        $daysInMonth = $targetDate->daysInMonth;

        // 当月最終日（例：30日）の数値を計算
        $lastDayOfMonth = $targetDate->daysInMonth;

        // お手伝い合計金額を日付別に取得する
        $dailyTotals = Calendar::with('chores')
            ->whereYear('chore_day', $targetDate->year)
            ->whereMonth('chore_day', $targetDate->month)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->chore_day)->format('Y-m-d');
            })
            ->map(function ($dayCalendars) {
                return $dayCalendars->sum(function ($calendar) {
                    return $calendar->chores->sum('price');
                });
            });

        // 当月累計金額
        $monthlyTotals = $dailyTotals->sum();

        // 今までの累計金額
        $grandTotals = Calendar::with('chores')
            ->get()
            ->sum(function ($calendar) {
                return $calendar->chores->sum('price');
            });

        return view('components.calendar', [
            'displayTitle' => $targetDate->format('Y年 n月'),
            'firstDayOfWeek' => $firstDayOfWeek,
            'daysInMonth' => $daysInMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'targetDate' => $targetDate,
            'today' => $today,
            'holidays' => $holidays,
            'dailyTotals' => $dailyTotals,
            'monthlyTotals' => $monthlyTotals,
            'grandTotals' => $grandTotals,
        ]);
    }
};
?>

<div class="calendar__area">
    
    <div class="calendar__header">
        <button wire:click="previousMonth">前月</button>
        <span wire:click="resetTothisMonth" title="今月に戻る">{{ $displayTitle }}</span>
        <button wire:click="nextMonth">次月</button>
    </div>

    <div class="calendar__summary">
        <div>
            <span class="summary__amount">今月：{{ $monthlyTotals }}円</span>
        </div>

        <div>
            <span class="summary__amount">累計：{{ $grandTotals }}円</span>
        </div>
    </div>

    <div class="calendar__table">
        <table>
            <thead>
                <tr>
                    <th class="sunday">日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th class="saturday">土</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $day = 1;
                @endphp

                @for ($i = 0; $i < 6; $i++)
                    <tr>
                    @for ($j = 0; $j < 7; $j++)

                        {{-- 1週目の1日より前のセルは空白にする 
                                $i === 0 ⇒ 1週目
                                $j < $firstDayOfWeek ⇒ その月の1日の曜日
                        --}}
                        @if ($i === 0 && $j < $firstDayOfWeek)
                            <td></td>
                        
                        {{-- 最終日を過ぎたらそのあとのセルは空にする --}}
                        @elseif ($day > $daysInMonth)
                            <td></td>


                        @else
                            @php
                                $currentDate = $targetDate->copy()->day($day);
                                $dateString = $currentDate->format('Y-m-d'); // 日付文字列作成

                                // ループ中の日付が「今日」かどうか判定
                                $isToday = ($today->format('Y-m-d') === $dateString);
                                $isHoliday = $holidays->isHoliday($currentDate); // 祝日判定

                                $isSunday = ($j === 0); // 日曜日判定
                                $isSaturday = ($j === 6); // 土曜日判定
                                $isLastDay = ($day === $lastDayOfMonth); //その月の最終日かどうかを判定

                                // 当該日の合計金額を取得（無ければ0円）
                                $totalAmount = $dailyTotals[$dateString] ?? 0;

                            @endphp
                                <td wire:click="openCreateView('{{ $dateString }}')" 
                                    {{-- 日付別に当てたいCSSの内容が異なることから、三項演算子を使って動的にclass名が変わるように設定 --}}
                                    class="{{ $isToday ? 'today' : '' }}
                                            {{ $isHoliday ? 'holiday' : '' }}
                                            {{ $isSunday ? 'sunday' : '' }}
                                            {{ $isSaturday ? 'saturday' : '' }}">
                                            
                                            
                                            <div class="{{ $isLastDay ? 'lastDay__area' : '' }}">
                                                {{ $day }}
                                                @if ($isLastDay)
                                                <span class="{{ $isLastDay ? 'lastDay__content' : '' }}">♥</span>
                                                @endif

                                                @if($totalAmount > 0)
                                                <div class="totalAmount__area">
                                                    {{ number_format($totalAmount) }}円
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                            @php
                                $day++;
                            @endphp
                        @endif
                    @endfor
                    </tr>
                    @if ($day > $daysInMonth)
                        @break
                    @endif
                @endfor
            </tbody>
        </table>
    </div>

    <livewire:registration-chore />

</div>