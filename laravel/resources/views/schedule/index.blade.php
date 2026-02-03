@extends('layouts.app')

@section('content')
    <section>
        <h2>Расписание</h2>
        <div class="card">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                    <tr>
                        <th>День</th>
                        <th>Время</th>
                        <th>Курс</th>
                        <th>Группа</th>
                        <th>Аудитория</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($schedules as $item)
                        <tr>
                            <td>{{ $item->weekday }}</td>
                            <td>{{ $item->time }}</td>
                            <td>{{ $item->course?->title ?? $item->course_id }}</td>
                            <td>{{ $item->group?->name ?? $item->group_id }}</td>
                            <td>{{ $item->room }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Нет занятий</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
