@extends('layouts.app')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/requests_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <div class="container__inner">
        @include('components.heading', ['title' => '申請一覧'])
        <div class="border">
            <ul class="border__list">
                <li><a href="{{ route('requests.list', ['tab'=>'pending_approval']) }}" class="{{ $tab === 'pending_approval' ? 'active-tab' : '' }}">承認待ち</a></li>
                <li><a href="{{ route('requests.list', ['tab'=>'approved']) }}" class="{{ $tab === 'approved' ? 'active-tab' : '' }}">承認済み</a></li>
            </ul>
        </div>

        <table class="list-table">
            <tr class="list-table__header-row">
                <th class="list-table__label">状態</th>
                <th class="list-table__label">名前</th>
                <th class="list-table__label">対象日時</th>
                <th class="list-table__label">申請理由</th>
                <th class="list-table__label">申請日時</th>
                <th class="list-table__label">詳細</th>
            </tr>
            @foreach($correctionRequests as $correctionRequest)
            <tr class="list-table__row">
                <td class="list-table__data">{{ $correctionRequest->is_approved ? '承認済み' : '承認待ち' }}</td>
                <td class="list-table__data">{{ $user->name }}</td>
                <td class="list-table__data">{{ $correctionRequest->date->isoformat('YYYY/MM/DD') }}</td>
                <td class="list-table__data">{{ $correctionRequest->reason }}</td>
                <td class="list-table__data">{{ $correctionRequest->created_at->isoformat('YYYY/MM/DD') }}</td>
                <td class="list-table__data">
                    <a class="list-table__detail-btn" href="{{ route('attendance.detail', ['id' => $correctionRequest->attendance_id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection