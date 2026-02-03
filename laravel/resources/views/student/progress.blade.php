@extends('layouts.app')

@section('content')
    <section>
        <h2>Успеваемость и посещаемость</h2>

        <div class="card">
            <h3>Мои оценки и посещаемость</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>День</th><th>Время</th><th>Курс</th><th>Группа</th><th>Оценка</th><th>Посещаемость</th></tr></thead>
                    <tbody>
                    @forelse($schedules as $schedule)
                        @php
                            $gradeRow = $grades->firstWhere('schedule_id', $schedule->id);
                            $attendanceRow = $attendance->get($schedule->id);
                        @endphp
                        <tr>
                            <td>{{ $schedule->weekday }}</td>
                            <td>{{ $schedule->time }}</td>
                            <td>{{ $schedule->course?->title ?? $schedule->course_id }}</td>
                            <td>{{ $schedule->group?->name ?? $schedule->group_id }}</td>
                            <td>{{ $gradeRow?->grade ?? '—' }}</td>
                            <td>
                                @if($attendanceRow)
                                    {{ $attendanceRow->status === 'present' ? 'Присутствовал' : 'Отсутствовал' }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Пока нет записей</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Средний балл по курсам</h3>
            <div class="grid cols-3">
                @forelse($avgGrades as $courseId => $avg)
                    <div class="stat-chip">
                        <div class="stat-label">{{ $courses->firstWhere('id', $courseId)?->title ?? $courseId }}</div>
                        <div class="stat-value">{{ $avg }}</div>
                    </div>
                @empty
                    <div class="empty">Пока нет оценок</div>
                @endforelse
            </div>
        </div>

        <div class="card" style="margin-top:1rem">
            <h3>Отзыв о пройденном курсе</h3>
            <form class="form-stack" method="post" action="{{ route('student.reviews.store') }}">
                @csrf
                <label>Курс</label>
                <select name="course_id" required>
                    <option value="">Выберите курс</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
                <label>Оценка</label>
                <select name="rating" required>
                    <option value="5">5 — Отлично</option>
                    <option value="4">4 — Хорошо</option>
                    <option value="3">3 — Нормально</option>
                    <option value="2">2 — Плохо</option>
                    <option value="1">1 — Очень плохо</option>
                </select>
                <label>Комментарий (необязательно)</label>
                <textarea name="comment" rows="3"></textarea>
                <div style="margin-top:.5rem"><button class="btn" type="submit">Оставить отзыв</button></div>
            </form>
        </div>
    </section>
@endsection
