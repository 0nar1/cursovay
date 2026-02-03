<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()->orderBy('title')->get();

        return view('courses.index', compact('courses'));
    }
}
