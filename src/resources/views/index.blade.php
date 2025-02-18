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
    <div class="container__inner">
        <div class="attendance__status">
            @if (!$attendance)
            <span class="status-label">勤務外</span>
            @elseif ($attendance->start_time && !$attendance->end_time)
            @if ($breakTime && is_null($breakTime->break_end))
            <span class="status-label">休憩中</span>
            @else
            <span class="status-label">出勤中</span>
            @endif
            @else
            <span class="status-label">退勤済</span>
            @endif
        </div>
        <div class="attendance__date">
            <h2 class="today-date">{{ $today->isoFormat('Y年M月D日(ddd)') }}</h2>
            <h3 class="current-time"><livewire:current-time /></h3>
        </div>
        <div class="attendance__panel">
            @if (!$attendance)
            <form class="attendance__button" action="{{ route('attendance.clock-in') }}" method="POST">
                @csrf
                <button class="attendance__button-submit" type="submit">出勤</button>
            </form>
            @elseif ($attendance->start_time && !$attendance->end_time)
            @if ($breakTime && is_null($breakTime->break_end))
            <form class="attendance__button" action="{{ route('attendance.break-end') }}" method="POST">
                @csrf
                <button class="attendance__button-submit attendance__button-break" type="submit">休憩戻</button>
            </form>
            @else
            <div class="button-container">
                <form class="attendance__button" action="{{ route('attendance.clock-out') }}" method="POST">
                    @csrf
                    <button class="attendance__button-submit" type="submit">退勤</button>
                </form>
                <form class="attendance__button" action="{{ route('attendance.break-start') }}" method="POST">
                    @csrf
                    <button class="attendance__button-submit attendance__button-break" type="submit">休憩入</button>
                </form>
            </div>
            @endif
            @else
            <p class="clock-out-message">お疲れ様でした。</p>
            @endif
        </div>
    </div>
</div>
@endsection