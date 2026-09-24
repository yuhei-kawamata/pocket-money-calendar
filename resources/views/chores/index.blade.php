@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/chores/index.css') }}">
@endsection

@section('title', 'お手伝い内容 登録・編集')

@section('content')

<div class="chore__area">
  <div class="error__massage__area">
    @error('name') 
      <span class="form__error">{{ $message }}</span> 
    @enderror

    @error('price') 
      <span class="form__error">{{ $message }}</span> 
    @enderror
  </div>

  <!-- 登録エリア -->
  <div class="chore__registration__area">

    <div class="chore__registration__title">

      お手伝い登録
    </div>
    
    <form class="chore__registration__form" method="POST" action="{{ route('chores.store') }}">
      @csrf
      <input type="text" name="name" placeholder="お手伝いの内容">
        
      <input type="number" name="price" placeholder="おこづかいの金額">

      <button type="submit"><i class="fa-solid fa-plus"></i> 登録</button>
    </form>
  </div>
    
  <!-- 一覧エリア -->
  <div class="chore__index__area">
    <div class="chore__index__title">
      お手伝い一覧
    </div>

    <div class="chore__index__table">
      <!-- ヘッダー（PC用） -->
      <div class="chore__header">
        <div class="col__name">内容</div>
        <div class="col__price">1回あたりの金額</div>
        <div class="col__btn"></div>
        <div class="col__btn"></div>
      </div>

      <!-- 一覧データ -->
      @foreach($chores as $chore)
      <div class="chore__row">
        <!-- 編集フォーム（内容・金額・編集ボタン） -->
        <form method="POST" action="{{ route('chores.update', $chore->id) }}" class="chore__form-update">
          @csrf
          @method('PATCH')

          <div class="col__name">
            <input type="text" name="name" value="{{ $chore->name }}">
          </div>

          <div class="col__price">
            <input type="number" name="price" value="{{ $chore->price }}"><span class="price__unit">円</span><span class="mobile__unit"> / 回</span>
          </div>

          <div class="col__btn col__btn--edit">
            <button class="edit__button" type="submit"><i class="fa-solid fa-pen"></i> 編集</button>
          </div>
        </form>

        <!-- 削除フォーム（削除ボタン） -->
        <div class="col__btn col__btn--delete">
          <form method="POST" action="{{ route('chores.destroy', $chore->id) }}">
            @csrf
            @method('DELETE')
            <button class="delete__button" type="submit" onclick="return confirm('削除しますか？')"><i class="fa-solid fa-trash"></i> 削除</button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection