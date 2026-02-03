<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Schedule::query()
            ->with(['course', 'group'])
            ->orderBy('weekday')
            ->orderBy('time');

        $user = $request->user();
        if ($user?->role === 'student') {
            $groupIds = $user->groups()->pluck('groups.id');
            $query->whereIn('group_id', $groupIds);
        } elseif ($user?->role === 'teacher') {
            $query->whereHas('group', fn ($q) => $q->where('teacher_id', $user->id));
        }

        $schedules = $query->get();

        return view('schedule.index', compact('schedules'));
    }
}
