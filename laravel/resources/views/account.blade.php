@extends('layouts.app')

@section('content')
    <section>
        <h2>Личный кабинет</h2>
        <form class="card" method="post" action="{{ route('account.update') }}">
            @csrf
            <div class="row">
                <div>
                    <label for="name">Имя</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>
            <div>
                <label>Роль</label>
                <div class="pill">{{ $user->role === 'admin' ? 'Администратор' : ($user->role === 'teacher' ? 'Преподаватель' : 'Студент') }}</div>
            </div>
            <div style="display:flex; gap:.5rem; margin-top:.75rem">
                <button class="btn" type="submit">Сохранить</button>
            </div>
        </form>
    </section>
@endsection
