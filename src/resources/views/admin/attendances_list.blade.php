@extends('layouts.app')

@section('title','管理者トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendances_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <div class="container__inner">
        @include('components.heading', ['title' => $selectedDate->format('Y年n月j日') . 'の勤怠'])
        <div class="date-navigation">
            <div class="date-navigation__content date-previous">
                <a href="{{ route('admin.attendance.list', ['year' => $previousDate->year, 'month' => $previousDate->month,
                'day' => $previousDate->day]) }}" class="date-link"><i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                    前日</a>
            </div>
            <div class="date-navigation__content   date-current"><i class="far fa-calendar-alt" style="color: #4B4B4B; margin-right: 5px;"></i>{{ $selectedDate->format('Y/m/d') }}</div>
            <div class="date-navigation__content  date-next">
                <a href="{{ route('admin.attendance.list', ['year' => $nextDate->year, 'month' => $nextDate->month, 'day' => $nextDate->day]) }}" class="date-link  next-link">翌日<i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                </a>
            </div>
        </div>

        <table class="list-table">
            <tr class="list-table__header-row">
                <th class="list-table__label">名前</th>
                <th class="list-table__label">出勤</th>
                <th class="list-table__label">退勤</th>
                <th class="list-table__label">休憩</th>
                <th class="list-table__label">合計</th>
                <th class="list-table__label">詳細</th>
            </tr>
            @foreach($attendances as $attendance)
            <tr class="list-table__row">
                <td class="list-table__data">{{ $attendance->user->name }}</td>
                <td class="list-table__data">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                <td class="list-table__data">
                    @if ($attendance->end_time)
                    {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                    @else
                    @endif
                </td>
                <td class="list-table__data">{{ $attendance->totalBreakTime }}</td>
                <td class="list-table__data">{{ $attendance->totalWorkTime }}</td>
                <td class="list-table__data">
                    <a class="list-table__detail-btn" href="{{ route('attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection