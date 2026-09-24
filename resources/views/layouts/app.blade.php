<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  @yield('css')
  <title>@yield('title', 'お手伝いカレンダー')</title>
</head>

<body>
  <header>
    <div class="header__area">
      <div class="header__inner">

        <a class="header__logo" href="{{ route('calendars.index') }}">お手伝いカレンダー</a>

        {{-- ハンバーガーメニュー --}}
        <input type="checkbox" id="drawer-checkbox" class="drawer-checkbox">
        
        <label for="drawer-checkbox" class="drawer-icon">
          <span></span>
          <span></span>
          <span></span>
        </label>
        
        <nav class="drawer-nav">
          <ul class="drawer-menu">
            <li><a href="{{ route('calendars.index') }}">カレンダー</a></li>
            <li><a href="{{ route('calendars.dashboard') }}">ダッシュボード</a></li>
            <li><a href="{{ route('chores.index') }}">お手伝い編集</a></li>
          </ul>
        </nav>

      </div>
    </div>
  </header>
    
  <main>
    @if(session('success'))
    <div class="alert__success__area">
      <div class="alert__success">
        {{ session('success') }}
      </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert__danger__area">
      <div class="alert__danger">
        {{ session('error') }}
      </div>
    </div>
    @endif
    
    @yield('content')
  </main>
</body>
</html>