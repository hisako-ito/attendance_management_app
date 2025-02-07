<header class="header">
    <div class="header__logo">
        <img src="{{ asset('img/logo.svg') }}" alt="ロゴ">
    </div>
    <nav class="header__nav">
        <ul>
            <li><a href="/attendance">勤怠</a></li>
            <li><a href="/attendance/list">勤怠一覧</a></li>
            <li><a href="/stamp_correction_request/list">申請</a></li>
            @if(Auth::check())
            <li>
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            @else
            <li><a href="/login">ログイン</a></li>
            @endif
        </ul>
    </nav>
</header>