@extends('layouts.app')

@section('title','スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendances_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <div class="container__inner">
        @include('components.heading', ['title' => 'スタッフ一覧'])
        <table class="list-table">
            <tr class="list-table__header-row">
                <th class="list-table__label">名前</th>
                <th class="list-table__label">メールアドレス</th>
                <th class="list-table__label">月次勤怠</th>
            </tr>
            @foreach($users as $user)
            <tr class="list-table__row">
                <td class="list-table__data">{{ $user->name }}</td>
                <td class="list-table__data">{{ $user->email }}</td>
                <td class="list-table__data">
                    <a class="list-table__detail-btn" href="/admin/attendance/staff/{{$user->id}}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection