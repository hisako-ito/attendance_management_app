@extends('layouts.app')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="correction-request-form">
    <div class="correction-request-form__inner">
        @include('components.heading', ['title' => '勤怠詳細'])
        <form action="/attendance/{id}" method="POST">
            <table class=" correction-request-form__table">
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">名前</th>
                    <td class="correction-request-form__data correction-request-form__data--name">{{ $user->name }}</td>
                </tr>
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">日付</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item-inputs">
                            <input class="correction-request-form__item-input" type="text" name="date" value="{{ $attendance->date->format('Y年') }}">
                            <input class="correction-request-form__item-input" type="text" name="date" value="{{ $attendance->date->format('n月j日') }}">
                        </div>
                    </td>
                </tr>
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">出勤・退勤</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item-inputs">
                            <input class="correction-request-form__item-input" type="text" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}">
                            <span>〜</span>
                            <input class="correction-request-form__item-input" type="text" name="end_time" value="{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}">
                        </div>
                    </td>
                </tr>
                @foreach($attendance->breaks as $break)
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">休憩</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item-inputs">
                            <input class="correction-request-form__item-input" type="text" name="break_start[]" value="{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}">
                            <span>〜</span>
                            <input class="correction-request-form__item-input" type="text" name="break_end[]" value="{{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}">
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">備考</th>
                    <td class="correction-request-form__data">
                        <textarea class="correction-request-form__textarea" name="reason" cols="50" rows="5">{{ old('reason') }}</textarea>
                    </td>
                </tr>
            </table>
            <div class="correction-request-form__btn-inner">
                <button type="submit" class="correction-request-form__btn btn">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection