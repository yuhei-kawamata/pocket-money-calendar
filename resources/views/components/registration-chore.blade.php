<?php

use App\Models\Calendar;
use App\Models\Chore;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{

    // デフォルトではポップアップは開かない設定
    public $isOpen = false;

    // フォームに入力する変数の定義
    public $chore_id = '';
    public $chore_day = '';
    public $selectedPrice = 0;

    // フラッシュメッセージ格納用の変数
    public $flashMessage = '';

    protected $rules = [
        'chore_id' => 'required|exists:chores,id',
        'chore_day' => 'required|date',
    ];

    // ポップアップを開く
    #[On('open-registration-chore')]
    public function openModal($date)
    {
        $this->chore_day = $date; // カレンダーから渡された日付をセット

        $this->resetvalidation();

        $this->reset([
            'chore_id',
            'selectedPrice',
            'flashMessage',
        ]);

        $this->isOpen = true;
    }

    // プルダウンでお手伝いを選択したら、そのお手伝いに紐づく金額を自動で取得する
    public function updatedChoreId($value)
    {
        if ($value) {
            $chore = Chore::find($value);
            $this->selectedPrice = $chore ? $chore->price : 0;
        } else {
            $this->selectedPrice = 0;
        }
    }

    // ポップアップを閉じる
    public function closeModal()
    {
        $this->isOpen = false;
        $this->flashMessage = '';
    }

    // 登録処理
    public function store()
    {
        $validated = $this->validate();

        $calendar = Calendar::create([
            'chore_id' => $validated['chore_id'],
            'chore_day' => $validated['chore_day'],
        ]);

        if (!empty($validated['chore_id'])) {
            $calendar->chores()->attach($validated['chore_id']);
        }

        // 入力フォームのみ初期化してモーダルは開いたままにする
        $this->reset([
            'chore_id',
            'selectedPrice',
        ]);

        $this->flashMessage = 'お手伝いを登録しました';

        $this->dispatch('calendar-created');
    }

    public function deleteChore($calendarId, $choreId)
    {
        $calendar = Calendar::find($calendarId);

        if ($calendar) {
            $calendar->chores()->detach($choreId);

            if ($calendar->chores()->count() === 0) {
                $calendar->delete();
            }

            $this->flashMessage = 'お手伝いを削除しました';

            $this->dispatch('calendar-created');
        }
    }

    /* 事前に登録されているお手伝い一覧と、その日にすでに登録されているお手伝い内容を
        取得してビューに渡す
    */
    public function render()
    {
        $registeredCalendars = collect();

        if ($this->chore_day) {
            $registeredCalendars = Calendar::with('chores')
                ->whereDate('chore_day', $this->chore_day)
                ->get();
        }

        return view('components.registration-chore', [
            'chores' => Chore::all(),
            'calendars' => $registeredCalendars,
        ]);
    }
};
?>

<div>
    @if($isOpen)
        <!-- モーダル背景（オーバーレイ） -->
        <div class="modal__overlay" wire:click.self="closeModal">
            
            <!-- モーダル本体 -->
            <div class="modal__content">
                <div class="modal__title">
                    {{ $chore_day }} のお手伝いを登録
                </div>

                {{-- フラッシュメッセージ表示エリア --}}
                @if ($flashMessage)
                    <div class="flashMessage__area">
                        {{ $flashMessage }}
                    </div>
                @endif

                <form wire:submit.prevent="store">
                    <!-- お手伝い選択 -->
                    <div class="form__group">
                        <label class="form__label">お手伝いの内容</label>
                        <select wire:model.live="chore_id" class="form__select">
                            <option value="">-- 選択してください --</option>
                            @foreach($chores as $chore)
                                <option value="{{ $chore->id }}">
                                    {{ $chore->name }} --{{ $chore->price }}円
                                </option>
                            @endforeach
                        </select>
                        @error('chore_id') 
                            <span class="form__error">{{ $message }}</span> 
                        @enderror
                    </div>
                    
                    <!-- 金額表示 -->
                    <div class="price__box">
                        <span class="price__label">おこづかいの金額：</span>
                        <strong class="price__value">{{ number_format($selectedPrice) }} 円</strong>
                    </div>
                    
                    <!-- ボタン領域 -->
                    <div class="modal__actions">
                        <button type="button" wire:click="closeModal" class="btn btn__secondary">
                            キャンセル
                        </button>
                        <button type="submit" class="btn btn__primary">
                        <i class="fa-solid fa-plus"></i>
                            登録
                        </button>
                    </div>
                </form>

                <!-- 登録済みお手伝い一覧表示エリア -->
                @php
                    // その日に登録されている chores が1件でもあるか判定
                    $hasRegisteredChores = $calendars->pluck('chores')->collapse()->isNotEmpty();
                @endphp

                @if($hasRegisteredChores)
                    <div class="registered__chores__section">
                        <div class="registered__title">登録済みのお手伝い</div>
                        <ul class="registered__list">
                            @foreach($calendars as $calendar)
                                @foreach($calendar->chores as $chore)
                                    <li class="registered__item">
                                        <div class="registered__info">
                                            <span class="chore__name">{{ $chore->name }}</span>
                                            <span class="chore__price">--{{ number_format($chore->price) }} 円</span>
                                            <button type="button" 
                                            wire:click="deleteChore({{ $calendar->id }}, {{ $chore->id }})" 
                                            class="btn btn__delete"
                                            wire:confirm="本当に削除しますか？">
                                            <i class="fa-solid fa-trash"></i>
                                            削除
                                        </button>
                                        </div>
                                    </li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>
    @endif
</div>