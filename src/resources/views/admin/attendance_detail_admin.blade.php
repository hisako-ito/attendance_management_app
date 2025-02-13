@extends('layouts.app')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance_detail.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="correction-request__alert">
    @if (session('message'))
    <div class="alert--success">
        {{ session('message') }}
    </div>
    @endif
</div>

<div class="container">
    <div class="container__inner">
        @include('components.heading', ['title' => '勤怠詳細'])
        <form action="{{ route('attendance.detail.store', ['id' => $attendance->id]) }}" method="POST">
            @csrf
            <table class=" correction-request-form__table">
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">名前</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__data--name">{{ $user->name }}</div>
                    </td>
                </tr>
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">日付</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item">
                            <div class="correction-request-form__item-inputs">
                                <input class="correction-request-form__item-input" type="text" name="date1" value="{{  \Carbon\Carbon::parse($attendance->date)->format('Y年') }}" readonly>
                                <span style="visibility:hidden;">〜</span>
                                <input class="correction-request-form__item-input" type="text" name="date2" value="{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}" readonly>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">出勤・退勤</th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item">
                            <div class="correction-request-form__item-inputs">
                                <input class="correction-request-form__item-input" type="text" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}">
                                <span>〜</span>
                                <input class="correction-request-form__item-input {{ $errors->has('end_time') ? 'is-valid' : '' }}" type="text" name="end_time"
                                    value="{{ !empty($attendance->end_time) ? old('end_time',\Carbon\Carbon::parse($attendance->end_time)->format('H:i')) : '' }}">
                            </div>
                            @if ($errors->has('start_time'))
                            <div class="form__error">
                                {{ $errors->first('start_time') }}
                            </div>
                            @endif
                            @if ($errors->has('end_time'))
                            <div class="form__error">
                                {{ $errors->first('end_time') }}
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @foreach($attendance->breaks as $index => $break)
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">
                        {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                    </th>
                    <td class="correction-request-form__data">
                        <div class="correction-request-form__item">
                            <div class="correction-request-form__item-inputs">
                                <input class="correction-request-form__item-input" type="text" name="break_start[]"
                                    value="{{ old('break_start.' . $index, \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}">
                                <span>〜</span>
                                <input class="correction-request-form__item-input {{ $errors->has('break_end.' . $index) ? 'is-valid' : '' }}" type="text" name="break_end[]"
                                    value="{{ old('break_end.' . $index, !empty($break->break_end) ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                            </div>
                            <div class="form__error">
                                @if ($errors->has('break_start.' . $index))
                                <div class="form__error">
                                    {{ $errors->first('break_start.' . $index) }}
                                </div>
                                @endif
                                @if ($errors->has('break_end.' . $index))
                                <div class="form__error">
                                    {{ $errors->first('break_end.' . $index) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="correction-request-form__row">
                    <th class="correction-request-form__label">備考</th>
                    <td class="correction-request-form__data">
                        <textarea class=" correction-request-form__textarea" name="reason" cols="50" rows="5">{{ old('reason') }}</textarea>
                        <div class="form__error">
                            @error('reason')
                            {{ $message }}
                            @enderror
                        </div>
                    </td>
                </tr>
            </table>
            <input class="correction-request-form__item-input" type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="correction-request-form__btn-inner">
                @if ($attendance->attendanceCorrectionRequests->isNotEmpty() && optional($attendance->attendanceCorrectionRequests->first())->isApproved() === false)
                <p class="correction-requested-message">*承認待ちのため修正はできません。</p>
                @else
                <button type="submit" class="correction-request-form__btn btn">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection