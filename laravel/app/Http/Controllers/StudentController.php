<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Grade;
use App\Models\Homework;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function homework(Request $request): View
    {
        $student = $request->user();
        $groupIds = $student->groups()->pluck('groups.id');

        $homeworks = Homework::query()
            ->with(['course', 'group', 'schedule'])
            ->whereIn('group_id', $groupIds)
            ->orderByDesc('assigned_at')
            ->get();

        return view('student.homework', compact('homeworks'));
    }

    public function progress(Request $request): View
    {
        $student = $request->user();
        $groupIds = $student->groups()->pluck('groups.id');

        $schedules = Schedule::query()
            ->with(['course', 'group'])
            ->whereIn('group_id', $groupIds)
            ->orderBy('weekday')
            ->orderBy('time')
            ->get();

        $grades = Grade::query()
            ->with(['schedule.course', 'schedule.group'])
            ->where('student_id', $student->id)
            ->get();

        $attendance = DB::table('attendances')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('schedule_id');

        $avgGrades = Grade::query()
            ->select('schedules.course_id', DB::raw('AVG(grades.grade) as avg_grade'))
            ->join('schedules', 'grades.schedule_id', '=', 'schedules.id')
            ->where('grades.student_id', $student->id)
            ->groupBy('schedules.course_id')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->course_id => round($row->avg_grade, 2)]);

        $courses = Course::query()->whereIn('id', $avgGrades->keys())->get();
        $existingReviews = CourseReview::query()
            ->where('student_id', $student->id)
            ->pluck('id', 'course_id');

        return view('student.progress', compact('schedules', 'grades', 'attendance', 'avgGrades', 'courses', 'existingReviews'));
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $student = $request->user();
        $data = $request->validate([
            'course_id' => ['required', 'string', 'exists:courses,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        $hasGrades = Grade::query()
            ->join('schedules', 'grades.schedule_id', '=', 'schedules.id')
            ->where('grades.student_id', $student->id)
            ->where('schedules.course_id', $data['course_id'])
            ->exists();

        if (!$hasGrades) {
            return back()->with('status', 'Нельзя оставить отзыв без оценок по курсу.');
        }

        CourseReview::updateOrCreate(
            ['student_id' => $student->id, 'course_id' => $data['course_id']],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?: null]
        );

        return back()->with('status', 'Отзыв сохранен.');
    }
}
