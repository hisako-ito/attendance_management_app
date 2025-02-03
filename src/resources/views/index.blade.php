@extends('layouts.app')

<!-- タイトル -->
@section('title','トップページ')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

<!-- 本体 -->
@include('components.header')
@section('content')
<div class="attendance__alert">
    // メッセージ機能
</div>

<div class="attendance__content">
    <div class="attendance__panel">
        <form class="attendance__button">
            <button class="attendance__button-submit" type="submit">出勤</button>
        </form>
        <form class="attendance__button">
            <button class="attendance__button-submit" type="submit">退勤</button>
        </form>
        <form class="attendance__button">
            <button class="attendance__button-submit" type="submit">休憩入</button>
        </form>
        <form class="attendance__button">
            <button class="attendance__button-submit" type="submit">休憩戻</button>
        </form>
        <p>お疲れ様でした。</p>
    </div>
</div>
@endsection