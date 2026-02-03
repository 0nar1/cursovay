@extends('layouts.app')

@section('content')
    <section>
        <h2>Вход</h2>
        <form class="card" method="post" action="{{ route('login.store') }}">
            @csrf
            <label for="loginEmail">Email</label>
            <input id="loginEmail" name="email" type="email" required value="{{ old('email') }}">
            <label for="loginPassword">Пароль</label>
            <input id="loginPassword" name="password" type="password" required>
            <div style="display:flex; gap:.5rem; margin-top:.75rem">
                <button class="btn" type="submit">Войти</button>
                <a class="btn ghost" href="{{ route('register') }}">Регистрация</a>
            </div>
            <div class="login-demo">
                <p>Демо-доступ:</p>
                <ul>
                    <li><strong>Админ:</strong> admin@top.local / admin</li>
                    <li><strong>Преподаватель:</strong> teacher1@top.local / teacher1</li>
                    <li><strong>Студент:</strong> student1@top.local / student1</li>
                </ul>
            </div>
        </form>
    </section>
@endsection
