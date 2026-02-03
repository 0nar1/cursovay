@extends('layouts.app')

@section('content')
    <section>
        <h2>Кабинет преподавателя</h2>

        <div class="card">
            <h3>Мои группы</h3>
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                @foreach($groups as $group)
                    <span class="pill green">{{ $group->name }}</span>
                @endforeach
                @if($groups->isEmpty())
                    <span class="muted">Нет назначенных групп</span>
                @endif
            </div>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Студенты</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Имя</th><th>Email</th><th>Группы</th></tr></thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>
                                @foreach($student->groups->intersect($groups) as $group)
                                    <span class="pill blue">{{ $group->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty">Студентов пока нет</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Посещаемость</h3>
            <form class="form-stack" method="post" action="{{ route('teacher.attendance.store') }}">
                @csrf
                <label>Занятие</label>
                <select name="schedule_id" required>
                    <option value="">Выберите занятие</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">{{ $schedule->weekday }} {{ $schedule->time }} — {{ $schedule->course?->title ?? $schedule->course_id }} ({{ $schedule->group?->name ?? $schedule->group_id }})</option>
                    @endforeach
                </select>
                <div class="grid cols-2" style="margin-top:.5rem">
                    @foreach($students as $student)
                        <div>
                            <label>{{ $student->name }}</label>
                            <select name="status_{{ $student->id }}">
                                <option value="present">Присутствовал</option>
                                <option value="absent">Отсутствовал</option>
                            </select>
                            <input name="comment_{{ $student->id }}" placeholder="Комментарий (если нужно)">
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:.5rem"><button class="btn" type="submit">Сохранить посещаемость</button></div>
            </form>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Оценки</h3>
            <form class="form-stack" method="post" action="{{ route('teacher.grades.store') }}">
                @csrf
                <label>Занятие</label>
                <select name="schedule_id" required>
                    <option value="">Выберите занятие</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">{{ $schedule->weekday }} {{ $schedule->time }} — {{ $schedule->course?->title ?? $schedule->course_id }} ({{ $schedule->group?->name ?? $schedule->group_id }})</option>
                    @endforeach
                </select>
                <div class="grid cols-2" style="margin-top:.5rem">
                    @foreach($students as $student)
                        <div>
                            <label>{{ $student->name }}</label>
                            <input type="number" min="1" max="12" step="1" name="grade_{{ $student->id }}" placeholder="Оценка 1-12">
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:.5rem"><button class="btn" type="submit">Сохранить оценки</button></div>
            </form>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Домашнее задание после занятия</h3>
            <form class="form-stack" method="post" action="{{ route('teacher.homework.store') }}">
                @csrf
                <label>Занятие</label>
                <select name="schedule_id" required>
                    <option value="">Выберите занятие</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">{{ $schedule->weekday }} {{ $schedule->time }} — {{ $schedule->course?->title ?? $schedule->course_id }} ({{ $schedule->group?->name ?? $schedule->group_id }})</option>
                    @endforeach
                </select>
                <label>Тема</label>
                <input name="title" required>
                <label>Описание</label>
                <textarea name="description" rows="3"></textarea>
                <div style="margin-top:.5rem"><button class="btn" type="submit">Выдать ДЗ</button></div>
            </form>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Комментарий для студента</h3>
            <form class="form-stack" method="post" action="{{ route('teacher.comment.store') }}">
                @csrf
                <label>Студент</label>
                <select name="student_id" required>
                    <option value="">Выберите студента</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
                <label>Группа (необязательно)</label>
                <select name="group_id">
                    <option value="">Без привязки</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <label>Занятие (необязательно)</label>
                <select name="schedule_id">
                    <option value="">Без привязки</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}">{{ $schedule->weekday }} {{ $schedule->time }} — {{ $schedule->group?->name ?? $schedule->group_id }}</option>
                    @endforeach
                </select>
                <label>Комментарий</label>
                <textarea name="comment" rows="3" required></textarea>
                <div style="margin-top:.5rem"><button class="btn" type="submit">Добавить комментарий</button></div>
            </form>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Последние выданные задания</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Занятие</th><th>Группа</th><th>Тема</th><th>Описание</th></tr></thead>
                    <tbody>
                    @forelse($homeworks as $hw)
                        <tr>
                            <td>{{ $hw->schedule?->weekday }} {{ $hw->schedule?->time }}</td>
                            <td>{{ $hw->group?->name ?? $hw->group_id }}</td>
                            <td>{{ $hw->title }}</td>
                            <td>{{ $hw->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Домашних заданий пока нет</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
