<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Homework;
use App\Models\Schedule;
use App\Models\StudentComment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $groups = Group::query()
            ->with(['course', 'students'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('name')
            ->get();

        $groupIds = $groups->pluck('id');
        $students = User::query()
            ->where('role', 'student')
            ->whereHas('groups', fn ($q) => $q->whereIn('groups.id', $groupIds))
            ->orderBy('name')
            ->get();

        $schedules = Schedule::query()
            ->with(['group', 'course'])
            ->whereIn('group_id', $groupIds)
            ->orderBy('weekday')
            ->orderBy('time')
            ->get();

        $homeworks = Homework::query()
            ->with(['group', 'course', 'schedule'])
            ->where('teacher_id', $teacher->id)
            ->latest('assigned_at')
            ->limit(10)
            ->get();

        return view('teacher.index', compact('groups', 'students', 'schedules', 'homeworks'));
    }

    public function storeHomework(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'string', 'exists:schedules,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $schedule = Schedule::with('group', 'course')->findOrFail($data['schedule_id']);

        Homework::create([
            'schedule_id' => $schedule->id,
            'course_id' => $schedule->course_id,
            'group_id' => $schedule->group_id,
            'teacher_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?: null,
        ]);

        return back()->with('status', 'Домашнее задание выдано.');
    }

    public function storeGrades(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'string', 'exists:schedules,id'],
        ]);

        $schedule = Schedule::with('group.students')->findOrFail($data['schedule_id']);

        foreach ($schedule->group->students as $student) {
            $gradeValue = $request->input('grade_'.$student->id);
            if ($gradeValue === null || $gradeValue === '') {
                continue;
            }

            Grade::updateOrCreate(
                ['schedule_id' => $schedule->id, 'student_id' => $student->id],
                ['grade' => (int) $gradeValue, 'teacher_id' => $request->user()->id]
            );
        }

        return back()->with('status', 'Оценки сохранены.');
    }

    public function storeAttendance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'string', 'exists:schedules,id'],
        ]);

        $schedule = Schedule::with('group.students')->findOrFail($data['schedule_id']);

        foreach ($schedule->group->students as $student) {
            $status = $request->input('status_'.$student->id, 'present');
            $comment = $request->input('comment_'.$student->id);

            Attendance::updateOrCreate(
                ['schedule_id' => $schedule->id, 'student_id' => $student->id],
                [
                    'status' => $status === 'absent' ? 'absent' : 'present',
                    'teacher_id' => $request->user()->id,
                    'comment' => $comment ?: null,
                ]
            );
        }

        return back()->with('status', 'Посещаемость сохранена.');
    }

    public function storeComment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'group_id' => ['nullable', 'string', 'exists:groups,id'],
            'schedule_id' => ['nullable', 'string', 'exists:schedules,id'],
            'comment' => ['required', 'string'],
        ]);

        StudentComment::create([
            'student_id' => $data['student_id'],
            'teacher_id' => $request->user()->id,
            'group_id' => $data['group_id'] ?? null,
            'schedule_id' => $data['schedule_id'] ?? null,
            'comment' => $data['comment'],
        ]);

        return back()->with('status', 'Комментарий добавлен.');
    }
}
