@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container center">
    @include('components.heading', ['title' => '勤怠一覧'])
    <div>
        <a href="{{ route('attendance.list', ['year' => $previousMonth->year, 'month' => $previousMonth->month]) }}">前月</a>
        <a href="{{ route('attendance.list', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}">翌月</a>
    </div>
    <table class="attendance__table">
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
            <td class="attendance__data">{{ $attendance->date->isoFormat('Y年M月D日(ddd)') }}</td>
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