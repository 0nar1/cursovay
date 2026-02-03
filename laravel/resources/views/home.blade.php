@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="panel">
            <h1>Академия «TOP» — обучение цифровым навыкам</h1>
            <p>Программы для детей и взрослых: программирование, дизайн, аналитика. Учитесь в удобном формате, следите за расписанием и управляйте обучением онлайн.</p>
            <div style="display:flex; gap:.5rem; margin-top:.75rem">
                <a class="btn" href="{{ route('courses.index') }}">Выбрать программу</a>
                <a class="btn ghost" href="{{ route('schedule.index') }}">Ближайшие занятия</a>
            </div>
        </div>
        <div class="panel">
            <div class="grid cols-2">
                <div class="card service-card-1"><h3>Онлайн‑платформа</h3><p class="muted">Материалы и задания доступны 24/7.</p></div>
                <div class="card service-card-2"><h3>Поддержка преподавателей</h3><p class="muted">Ответы на вопросы и разбор задач.</p></div>
                <div class="card service-card-3"><h3>Современная программа</h3><p class="muted">Актуальные технологии и практики.</p></div>
                <div class="card service-card-4"><h3>Карьерное консультирование</h3><p class="muted">Помогаем со стажировками и проектами.</p></div>
            </div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Программы</h2>
        <div class="grid cols-4">
            <div class="card program-card-1"><h3>7–8 лет</h3><p class="muted">Знакомство с логикой и визуальным кодом.</p><a class="btn blue" href="{{ route('courses.index') }}">Подробнее</a></div>
            <div class="card program-card-2"><h3>9–12 лет</h3><p class="muted">Scratch, основы Python и веб‑страниц.</p><a class="btn green" href="{{ route('courses.index') }}">Подробнее</a></div>
            <div class="card program-card-3"><h3>13–14 лет</h3><p class="muted">JavaScript, алгоритмы, проектная работа.</p><a class="btn orange" href="{{ route('courses.index') }}">Подробнее</a></div>
            <div class="card program-card-4"><h3>15–55 лет</h3><p class="muted">Профессии: фронтенд, аналитик, QA, дизайн.</p><a class="btn purple" href="{{ route('courses.index') }}">Подробнее</a></div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Сервисы</h2>
        <div class="grid cols-2">
            <div class="card service-card-1"><h3>Студенту</h3><p class="muted">Личный кабинет, прогресс, домашние задания.</p></div>
            <div class="card service-card-2"><h3>Абитуриенту</h3><p class="muted">Подбор программы и консультации.</p></div>
            <div class="card service-card-3"><h3>Педагогу</h3><p class="muted">Инструменты для проведения занятий.</p></div>
            <div class="card service-card-4"><h3>Онлайн‑оплата</h3><p class="muted">Удобные и безопасные способы оплаты.</p></div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Новости</h2>
        <div class="grid cols-2">
            <div class="card news-card"><h3>Скидки ко дню рождения</h3><p class="muted">Именинникам — минус 15% на обучение в месяц праздника.</p><a class="btn ghost" href="{{ route('feedback.index') }}">Уточнить детали</a></div>
            <div class="card news-card"><h3>Акция 1+1</h3><p class="muted">Приведи друга и оба получите бонусы на обучение.</p><a class="btn ghost" href="{{ route('feedback.index') }}">Задать вопрос</a></div>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Контакты</h2>
        <div class="card">
            <p><strong>Телефон:</strong> +7 (495) 743‑53‑05</p>
            <p><strong>Адрес:</strong> Сергиев Посад, пр. Красной Армии, 212А, корп. 1, офис 12</p>
            <p class="muted">Задайте вопрос через форму <a href="{{ route('feedback.index') }}">обратной связи</a> — ответим в ближайшее время.</p>
        </div>
    </section>
@endsection
