@extends('layouts.app')

@section('title','会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/verify.css')  }}">
@endsection

@section('content')

<div class="container">
    <div class="container__inner">
        @include('components.heading', ['title' => 'メール認証のご案内'])
        @if (session('resent'))
        <p class="notice_resend--p" role="alert">
            新規認証メールを送信しました！
        </p>
        @endif
        <p class="alert_resend--p">
            このページを閲覧するには、Eメールによる認証が必要です。
            もし認証用のメールが届かない場合、
        <form class="mail_resend--form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="mail_resend--button">こちらのリンク</button>をクリックして、認証メールを再送信の上、ご確認をお願いいたします。
        </form>
        </p>
    </div>
</div>
@endsection