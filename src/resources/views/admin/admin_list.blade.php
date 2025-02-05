@extends('layouts.app')

@section('title','管理者トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin_list.css')  }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <h2>{{ $now->format('Y年m月d日') }}</h2>
</div>
@endsection