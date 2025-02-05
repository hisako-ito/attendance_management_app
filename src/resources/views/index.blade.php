@extends('layouts.app')

@section('title','トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="attendance__alert">
    @if (session('message'))
    <div class="alert--success">
        {{ session('message') }}
    </div>
    @endif
</div>

<div class="container">
    <h2>{{ $now->format('Y年m月d日') }}</h2>
    <h3>{{ $now->format('H:i') }}</h3>
    <div class="attendance__panel">
        <form class="attendance__button" action="{{ route('attendance.clock-in') }}" method="POST">
            @csrf
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