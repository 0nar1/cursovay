<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Компьютерная академия TOP — Информационная система</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container header-content">
        <div class="brand">
            <span class="brand-logo" aria-hidden="true">⬢</span>
            <span class="brand-name">Академия TOP</span>
        </div>
        <nav class="nav" aria-label="Главная навигация">
            <button class="nav-toggle" aria-expanded="false" aria-controls="nav-menu">Меню</button>
            <ul id="nav-menu" class="nav-menu">
                <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Главная</a></li>
                <li><a href="{{ route('courses.index') }}" @if(request()->routeIs('courses.index')) aria-current="page" @endif>Курсы</a></li>
                <li><a href="{{ route('schedule.index') }}" @if(request()->routeIs('schedule.index')) aria-current="page" @endif>Расписание</a></li>
                <li><a href="{{ route('feedback.index') }}" @if(request()->routeIs('feedback.index')) aria-current="page" @endif>Обратная связь</a></li>
                @auth
                    <li><a href="{{ route('account.show') }}" @if(request()->routeIs('account.show')) aria-current="page" @endif>Личный кабинет</a></li>
                    @if(auth()->user()->role === 'student')
                        <li><a href="{{ route('student.homework') }}" @if(request()->routeIs('student.homework')) aria-current="page" @endif>Мои задания</a></li>
                        <li><a href="{{ route('student.progress') }}" @if(request()->routeIs('student.progress')) aria-current="page" @endif>Успеваемость</a></li>
                    @endif
                    @if(auth()->user()->role === 'teacher')
                        <li><a href="{{ route('teacher.index') }}" @if(request()->routeIs('teacher.index')) aria-current="page" @endif>Кабинет педагога</a></li>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.index') }}" @if(request()->routeIs('admin.index')) aria-current="page" @endif>Админ-панель</a></li>
                    @endif
                    <li>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn ghost" type="submit">Выйти</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Вход</a></li>
                @endauth
            </ul>
        </nav>
    </div>
</header>

<main class="container" tabindex="-1">
    @if(session('status'))
        <div class="card" style="margin-bottom:1rem">
            <strong>{{ session('status') }}</strong>
        </div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container footer-content">
        <p>© <span id="year"></span> Академия TOP. Учебный прототип.</p>
    </div>
</footer>

<script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
