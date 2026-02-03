@extends('layouts.app')

@section('content')
    <section>
        <h2>Регистрация</h2>
        <form class="card" method="post" action="{{ route('register.store') }}">
            @csrf
            <div class="row">
                <div>
                    <label for="regName">Имя</label>
                    <input id="regName" name="name" required value="{{ old('name') }}">
                </div>
                <div>
                    <label for="regEmail">Email</label>
                    <input id="regEmail" name="email" type="email" required value="{{ old('email') }}">
                </div>
            </div>
            <label for="regPassword">Пароль</label>
            <input id="regPassword" name="password" type="password" minlength="6" required>
            <p class="input-hint">Минимум 6 символов, учитываются буквы, цифры и знаки.</p>
            <div style="display:flex; gap:.5rem; margin-top:.75rem">
                <button class="btn" type="submit">Создать аккаунт</button>
                <a class="btn ghost" href="{{ route('login') }}">У меня уже есть аккаунт</a>
            </div>
        </form>
    </section>
@endsection
