<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">

  {{-- フォント --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@500;700&display=swap" rel="stylesheet">

  <title>お手伝いカレンダーアプリ ユーザー登録</title>
</head>
<body>
  <div class="register">

    <div class="register__area">

      <div class="register__title">ユーザー登録</div>
    
      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="register__input__area">
          <label for="name">名前</label>
          <input class="input__area" type="text" id="name" name="name" value="{{ old('name') }}">

          @error('name')
            <span class="alert__danger">
              {{ $message }}
            </span>
          @enderror
        </div>

        <div class="register__input__area">
          <label for="email">メールアドレス</label>
          <input class="input__area" type="email" id="email" name="email" value="{{ old('email') }}">

          @error('email')
            <span class="alert__danger">
              {{ $message }}
            </span>
          @enderror
        </div>

        <div class="register__input__area">
          <label for="password">パスワード</label>
          <input class="input__area" type="password" id="password" name="password">

          @error('password')
            <span class="alert__danger">
              {{ $message }}
            </span>
          @enderror
        </div>

        <div class="register__input__area">
          <label for="password_confirmaiton">パスワード（確認）</label>
          <input class="input__area" type="password" id="password_confirmation" name="password_confirmation">

          @error('password_confirmation')
            <span class="alert__danger">
              {{ $message }}
            </span>
          @enderror
        </div>

        <div class="register__input__area">
          <button class="register__button" type="submit">登録</button>
        </div>
      </form>

      <div class="link__login">
        <a href="/login">アカウントをお持ちの方はこちら</a>
      </div>
    </div>
</body>
</html>