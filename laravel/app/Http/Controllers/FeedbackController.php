<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        return view('feedback');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Feedback::create([
            'name' => $request->user()?->name,
            'email' => $request->user()?->email,
            'topic' => $data['topic'] ?? null,
            'course' => $data['course'] ?? null,
            'message' => $data['message'],
        ]);

        return back()->with('status', 'Спасибо! Сообщение отправлено.');
    }
}
