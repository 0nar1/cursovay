@extends('layouts.app')

@section('content')
    <section>
        <h2>Управление (администратор)</h2>

        <div class="admin-intro">
            <div class="card admin-auto-save">
                <h3>✅ Серверные данные</h3>
                <p>Изменения сохраняются в базе данных MySQL и доступны всем пользователям.</p>
            </div>
            <div class="card admin-stats">
                <div class="stat-chip blue">
                    <div class="stat-label">Пользователи</div>
                    <div class="stat-value">{{ $users->count() }}</div>
                </div>
                <div class="stat-chip green">
                    <div class="stat-label">Преподаватели</div>
                    <div class="stat-value">{{ $users->where('role', 'teacher')->count() }}</div>
                </div>
                <div class="stat-chip orange">
                    <div class="stat-label">Студенты</div>
                    <div class="stat-value">{{ $users->where('role', 'student')->count() }}</div>
                </div>
                <div class="stat-chip purple">
                    <div class="stat-label">Группы</div>
                    <div class="stat-value">{{ $groups->count() }}</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-label">Занятия</div>
                    <div class="stat-value">{{ $schedules->count() }}</div>
                </div>
            </div>
        </div>

        <div class="admin-columns">
            <div class="admin-stack">
                <div class="card admin-section">
                    <h3>Группы</h3>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                            <tr><th>Название</th><th>Курс</th><th>Преподаватель</th><th>Студенты</th><th></th></tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>{{ $group->course?->title ?? $group->course_id }}</td>
                                    <td>
                                        <form method="post" action="{{ route('admin.groups.teacher', $group->id) }}">
                                            @csrf
                                            <select name="teacher_id" class="assign-select" onchange="this.form.submit()">
                                                <option value="">Не назначен</option>
                                                @foreach($users->where('role','teacher') as $teacher)
                                                    <option value="{{ $teacher->id }}" @selected($group->teacher_id === $teacher->id)>{{ $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $group->students->count() }}</td>
                                    <td>
                                        <form method="post" action="{{ route('admin.groups.delete', $group->id) }}">
                                            @csrf
                                            <button class="btn danger btn-icon" type="submit" aria-label="Удалить группу" title="Удалить">🗑</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form class="form-stack" method="post" action="{{ route('admin.groups.store') }}" style="margin-top:.5rem">
                        @csrf
                        <div class="row">
                            <input name="name" placeholder="Название группы" required>
                            <select name="course_id" required>
                                <option value="">Выберите курс</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <select name="teacher_id">
                                <option value="">Выберите преподавателя</option>
                                @foreach($users->where('role','teacher') as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <textarea name="description" rows="2" placeholder="Описание группы"></textarea>
                        <div style="margin-top:.5rem"><button class="btn" type="submit">Создать группу</button></div>
                    </form>
                </div>

                <div class="card admin-section">
                    <h3>Назначение студентов в группы</h3>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead><tr><th>Имя</th><th>Email</th><th>Группы</th><th>Действия</th></tr></thead>
                            <tbody>
                            @foreach($users->where('role','student') as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        @foreach($student->groups as $group)
                                            <span class="pill">{{ $group->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <form method="post" action="{{ route('admin.groups.students') }}">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            <select class="assign-select" name="group_id" onchange="this.form.submit()">
                                                <option value="">Добавить в группу</option>
                                                @foreach($groups as $group)
                                                    @if(!$student->groups->contains($group))
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card admin-section">
                    <h3>Пользователи</h3>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead><tr><th>Имя</th><th>Роль</th><th>Email</th><th></th></tr></thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->role === 'admin' ? 'Администратор' : ($user->role === 'teacher' ? 'Преподаватель' : 'Студент') }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role !== 'admin')
                                            <form method="post" action="{{ route('admin.users.delete', $user->id) }}">
                                                @csrf
                                                <button class="btn danger btn-icon" type="submit" aria-label="Удалить пользователя" title="Удалить">🗑</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form class="form-stack" method="post" action="{{ route('admin.users.store') }}" style="margin-top:.5rem">
                        @csrf
                        <div class="row">
                            <input name="name" placeholder="Имя" required>
                            <input name="email" type="email" placeholder="Email" required>
                        </div>
                        <div class="row">
                            <input name="password" type="password" placeholder="Пароль" required>
                            <select name="role" required>
                                <option value="student">Студент</option>
                                <option value="teacher">Преподаватель</option>
                                <option value="admin">Администратор</option>
                            </select>
                        </div>
                        <div style="margin-top:.5rem"><button class="btn" type="submit">Создать пользователя</button></div>
                    </form>
                </div>
            </div>

            <div class="admin-stack">
                <div class="card admin-section">
                    <h3>Расписание</h3>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead><tr><th>День</th><th>Время</th><th>Курс</th><th>Группа</th><th>Ауд.</th><th></th></tr></thead>
                            <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->weekday }}</td>
                                    <td>{{ $schedule->time }}</td>
                                    <td>{{ $schedule->course?->title ?? $schedule->course_id }}</td>
                                    <td>{{ $schedule->group?->name ?? $schedule->group_id }}</td>
                                    <td>{{ $schedule->room }}</td>
                                    <td>
                                        <form method="post" action="{{ route('admin.schedule.delete', $schedule->id) }}">
                                            @csrf
                                            <button class="btn danger btn-icon" type="submit" aria-label="Удалить занятие" title="Удалить">🗑</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form class="form-stack" method="post" action="{{ route('admin.schedule.store') }}" style="margin-top:.5rem">
                        @csrf
                        <div class="row">
                            <input name="weekday" placeholder="День" required>
                            <input name="time" placeholder="Время" required>
                        </div>
                        <div class="row">
                            <select name="course_id" required>
                                <option value="">Выберите курс</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            <select name="group_id" required>
                                <option value="">Выберите группу</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input name="room" placeholder="Аудитория" required>
                        <div style="margin-top:.5rem"><button class="btn" type="submit">Добавить</button></div>
                    </form>
                </div>

                <div class="card admin-section">
                    <h3>Курсы</h3>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead><tr><th>ID</th><th>Название</th><th>Уровень</th><th>Длит.</th><th></th></tr></thead>
                            <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>{{ $course->id }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->level }}</td>
                                    <td>{{ $course->duration }}</td>
                                    <td>
                                        <form method="post" action="{{ route('admin.courses.delete', $course->id) }}">
                                            @csrf
                                            <button class="btn danger btn-icon" type="submit" aria-label="Удалить курс" title="Удалить">🗑</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form class="form-stack" method="post" action="{{ route('admin.courses.store') }}">
                        @csrf
                        <div class="row">
                            <input name="id" placeholder="id курса (латиница)">
                            <input name="title" placeholder="Название" required>
                        </div>
                        <div class="row">
                            <input name="level" placeholder="Уровень (Начальный/Средний/Продвинутый)">
                            <input name="duration" placeholder="Длительность (недели)">
                        </div>
                        <input name="tags" placeholder="Теги через запятую">
                        <textarea name="description" rows="3" placeholder="Описание"></textarea>
                        <div style="margin-top:.5rem"><button class="btn" type="submit">Добавить курс</button></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
