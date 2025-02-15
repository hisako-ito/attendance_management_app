@extends('layouts.app')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/approve_form.css')  }}">
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
        <form action="{{ route('request.approve.store', ['id' => $correctionRequest->id]) }}" method="POST">
            @csrf
            <table class="form__table">
                <tr class="form__row">
                    <th class="form__label">名前</th>
                    <td class="form__data">
                        <div class="form__data--name">{{ $correctionRequest->user->name }}</div>
                    </td>
                </tr>
                <tr class="form__row">
                    <th class="form__label">日付</th>
                    <td class="form__data">
                        <div class="form__item">
                            <div class="form__item-inputs">
                                <input class="form__item-input" type="text" name="date1" value="{{  \Carbon\Carbon::parse($correctionRequest->date)->format('Y年') }}" readonly>
                                <span style="visibility:hidden;">〜</span>
                                <input class="form__item-input" type="text" name="date2" value="{{ \Carbon\Carbon::parse($correctionRequest->date)->format('n月j日') }}" readonly>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="form__row">
                    <th class="form__label">出勤・退勤</th>
                    <td class="form__data">
                        <div class="form__item">
                            <div class="form__item-inputs">
                                <input class="form__item-input" type="text" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($correctionRequest->start_time)->format('H:i')) }}" readonly>
                                <span>〜</span>
                                <input class="form__item-input {{ $errors->has('end_time') ? 'is-valid' : '' }}" type="text" name="end_time"
                                    value="{{ !empty($correctionRequest->end_time) ? old('end_time',\Carbon\Carbon::parse($correctionRequest->end_time)->format('H:i')) : '' }}" readonly>
                            </div>
                        </div>
                    </td>
                </tr>
                @foreach($correctionRequest->breakCorrectionRequests as $index => $breakCorrectionRequest)
                <tr class="form__row">
                    <th class="form__label">
                        {{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}
                    </th>
                    <td class="form__data">
                        <div class="form__item">
                            <div class="form__item-inputs">
                                <input class="form__item-input" type="text" name="break_start[]"
                                    value="{{ old('break_start.' . $index, \Carbon\Carbon::parse($breakCorrectionRequest->break_start)->format('H:i')) }}" readonly>
                                <span>〜</span>
                                <input class="form__item-input {{ $errors->has('break_end.' . $index) ? 'is-valid' : '' }}" type="text" name="break_end[]"
                                    value="{{ old('break_end.' . $index, !empty($breakCorrectionRequest->break_end) ? \Carbon\Carbon::parse($breakCorrectionRequest->break_end)->format('H:i') : '') }}" readonly>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                <tr class="form__row">
                    <th class="form__label">備考</th>
                    <td class="form__data">
                        <textarea class=" form__textarea" name="reason" cols="50" rows="5" readonly>{{ $correctionRequest->reason }}</textarea>
                    </td>
                </tr>
            </table>
            <div class="form__btn-inner">
                @if ($correctionRequest->is_approved)
                <button disabled type="submit" class="form__btn btn btn-approved">承認済み</button>
                @else
                <button type="submit" class="form__btn btn">承認</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection