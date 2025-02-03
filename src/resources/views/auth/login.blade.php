@extends('layouts.app')

@section('title','ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css')  }}">
@endsection

@section('content')
<header class="header">
    <div class="header__logo">
        <a href="/"><img src="{{ asset('img/logo.svg') }}" alt="ロゴ"></a>
    </div>
</header>
<form action="/login" method="post" class="authenticate center">
    @csrf
    <h1 class="page__title">ログイン</h1>
    <label for="mail" class="entry__name">メールアドレス</label>
    <input name="email" id="mail" type="email" class="input" value="{{ old('email') }}">
    <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <label for="password" class="entry__name">パスワード</label>
    <input name="password" id="password" type="password" class="input">
    <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="btn btn--big">ログインする</button>
    <a href="/register" class="link">会員登録はこちら</a>
</form>
@endsection