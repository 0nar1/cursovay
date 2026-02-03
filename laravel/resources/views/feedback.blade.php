@extends('layouts.app')

@section('content')
    <section>
        <h2>Обратная связь</h2>
        <form class="card" method="post" action="{{ route('feedback.store') }}">
            @csrf
            <div class="row">
                <div>
                    <label for="topic">Тема</label>
                    <input id="topic" name="topic" placeholder="Вопрос по курсу..." value="{{ old('topic') }}">
                </div>
                <div>
                    <label for="course">Курс</label>
                    <input id="course" name="course" placeholder="Например: JavaScript Базовый" value="{{ old('course') }}">
                </div>
            </div>
            <div>
                <label for="message">Сообщение</label>
                <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
            </div>
            <div style="display:flex; gap:.5rem; margin-top:.75rem">
                <button class="btn" type="submit">Отправить</button>
            </div>
        </form>
    </section>
@endsection
