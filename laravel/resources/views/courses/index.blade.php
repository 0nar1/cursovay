@extends('layouts.app')

@section('content')
    <section>
        <h2>Каталог курсов</h2>
        <div class="grid cols-3">
            @forelse($courses as $course)
                <div class="card">
                    <h3>{{ $course->title }}</h3>
                    <p class="muted">Уровень: {{ $course->level ?? '—' }} • {{ $course->duration ?? '—' }}</p>
                    <p>{{ $course->description }}</p>
                    <div>
                        @foreach(($course->tags ?? []) as $tag)
                            <span class="pill">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty">Курсы пока не добавлены</div>
            @endforelse
        </div>
    </section>
@endsection
