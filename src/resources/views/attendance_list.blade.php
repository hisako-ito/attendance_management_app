@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container center">
    @include('components.heading', ['title' => '勤怠一覧'])
    <div class="month-container">
        <div class="month-content previous-month"><a href="{{ $previousMonth->year == now()->year && $previousMonth->month == now()->month
            ? route('attendance.list')
            : route('attendance.list', ['year' => $previousMonth->year, 'month' => $previousMonth->month]) }}" class="month-link"><i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                前月</a>
        </div>
        <div class=" month-content current-month"><i class="far fa-calendar-alt" style="color: #4B4B4B; margin-right: 5px;"></i>{{ $currentMonth->format('Y/m') }}</div>
        <div class="month-content">
            <a href="{{ $nextMonth->year == now()->year && $nextMonth->month == now()->month
            ? route('attendance.list')
            : route('attendance.list', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="month-link next-month-link">翌月<i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
            </a>
        </div>
    </div>

    <table class=" attendance__table">
        <tr class="attendance__header-row">
            <th class="attendance__label">日付</th>
            <th class="attendance__label">出勤</th>
            <th class="attendance__label">退勤</th>
            <th class="attendance__label">休憩</th>
            <th class="attendance__label">合計</th>
            <th class="attendance__label">詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr class="attendance__row">
            <td class="attendance__data">{{ $attendance->date->isoformat('MM/DD(ddd)') }}</td>
            <td class="attendance__data">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
            <td class="attendance__data">
                @if ($attendance->end_time)
                {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                @else
                @endif
            </td>
            <td class="attendance__data">{{ $attendance->totalBreakTime }}</td>
            <td class="attendance__data">{{ $attendance->totalWorkTime }}</td>
            <td class="attendance__data">
                <a class="attendance__detail-btn" href="/attendance/{{$attendance->id}}">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection