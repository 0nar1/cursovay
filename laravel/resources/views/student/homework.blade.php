@extends('layouts.app')

@section('content')
    <section>
        <h2>Мои задания</h2>
        <div class="card">
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Курс</th><th>Группа</th><th>Задание</th><th>Описание</th><th>Занятие</th></tr></thead>
                    <tbody>
                    @forelse($homeworks as $hw)
                        <tr>
                            <td>{{ $hw->course?->title ?? $hw->course_id }}</td>
                            <td>{{ $hw->group?->name ?? $hw->group_id }}</td>
                            <td>{{ $hw->title }}</td>
                            <td>{{ $hw->description }}</td>
                            <td>{{ $hw->schedule?->weekday }} {{ $hw->schedule?->time }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Пока нет заданий</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
