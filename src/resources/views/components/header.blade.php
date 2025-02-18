<header class="header">
    <div class="header__logo">
        <img src="{{ asset('img/logo.svg') }}" alt="ロゴ">
    </div>
    <nav class="header__nav">
        <ul>
            @if (Auth::guard('admin')->check())
            <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
            <li><a href="{{ route('users.list') }}">スタッフ一覧</a></li>
            <li><a href="{{ route('requests.list') }}">申請一覧</a></li>
            <li>
                <form action="/admin/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            @elseif (Auth::guard('web')->check())
            <li><a href="/attendance">勤怠</a></li>
            <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
            <li><a href="{{ route('requests.list') }}">申請</a></li>
            <li>
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            @endif
        </ul>
    </nav>
</header>