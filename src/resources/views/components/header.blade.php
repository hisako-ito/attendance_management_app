<header class="header">
    <div class="header__logo">
        <a href="/"><img src="{{ asset('img/logo.svg') }}" alt="ロゴ"></a>
    </div>
    <nav class="header__nav">
        <ul>
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
            <li><a href="/register">会員登録</a></li>
            <li><a href="/mypage">マイページ</a></li>
            <a href="/sell">
                <li class="header__btn">出品</li>
            </a>
        </ul>
    </nav>
</header>