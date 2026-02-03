<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('name')->get();
        $courses = Course::query()->orderBy('title')->get();
        $groups = Group::query()->with(['course', 'teacher', 'students'])->orderBy('name')->get();
        $schedules = Schedule::query()->with(['course', 'group'])->orderBy('weekday')->orderBy('time')->get();

        return view('admin.index', compact('users', 'courses', 'groups', 'schedules'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id' => ['nullable', 'string', 'max:64'],
            'title' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
        ]);

        $id = $data['id'] ?: Str::slug($data['title']);
        if (Course::whereKey($id)->exists()) {
            $id = $id.'-'.Str::lower(Str::random(4));
        }

        Course::create([
            'id' => $id,
            'title' => $data['title'],
            'level' => $data['level'] ?: null,
            'duration' => $data['duration'] ?: null,
            'description' => $data['description'] ?: null,
            'tags' => $data['tags'] ? array_values(array_filter(array_map('trim', explode(',', $data['tags'])))) : null,
        ]);

        return back()->with('status', 'Курс добавлен.');
    }

    public function deleteCourse(string $courseId): RedirectResponse
    {
        Course::whereKey($courseId)->delete();

        return back()->with('status', 'Курс удален.');
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'string', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = Str::slug($data['name']);
        $id = $slug ?: Str::lower(Str::random(6));
        if (Group::whereKey($id)->exists()) {
            $id = $id.'-'.Str::lower(Str::random(4));
        }

        Group::create([
            'id' => $id,
            'name' => $data['name'],
            'course_id' => $data['course_id'],
            'teacher_id' => $data['teacher_id'] ?: null,
            'description' => $data['description'] ?: null,
        ]);

        return back()->with('status', 'Группа создана.');
    }

    public function deleteGroup(string $groupId): RedirectResponse
    {
        Group::whereKey($groupId)->delete();

        return back()->with('status', 'Группа удалена.');
    }

    public function assignTeacher(Request $request, string $groupId): RedirectResponse
    {
        $data = $request->validate([
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        Group::whereKey($groupId)->update(['teacher_id' => $data['teacher_id'] ?: null]);

        return back()->with('status', 'Преподаватель назначен.');
    }

    public function assignStudent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'group_id' => ['required', 'string', 'exists:groups,id'],
        ]);

        $group = Group::findOrFail($data['group_id']);
        $group->students()->syncWithoutDetaching([$data['student_id']]);

        return back()->with('status', 'Студент добавлен в группу.');
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'weekday' => ['required', 'string', 'max:16'],
            'time' => ['required', 'string', 'max:16'],
            'course_id' => ['required', 'string', 'exists:courses,id'],
            'group_id' => ['required', 'string', 'exists:groups,id'],
            'room' => ['required', 'string', 'max:64'],
        ]);

        $id = 'sch-'.Str::lower(Str::random(6));
        if (Schedule::whereKey($id)->exists()) {
            $id = $id.'-'.Str::lower(Str::random(3));
        }

        Schedule::create([
            'id' => $id,
            'course_id' => $data['course_id'],
            'group_id' => $data['group_id'],
            'weekday' => $data['weekday'],
            'time' => $data['time'],
            'room' => $data['room'],
        ]);

        return back()->with('status', 'Занятие добавлено.');
    }

    public function deleteSchedule(string $scheduleId): RedirectResponse
    {
        Schedule::whereKey($scheduleId)->delete();

        return back()->with('status', 'Занятие удалено.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,teacher,student'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return back()->with('status', 'Пользователь создан.');
    }

    public function deleteUser(string $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        if ($user->role === 'admin') {
            return back()->with('status', 'Администратора удалять нельзя.');
        }

        $user->delete();

        return back()->with('status', 'Пользователь удален.');
    }
}
