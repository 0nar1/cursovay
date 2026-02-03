<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Feedback;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $path = base_path('../BD.json');
        if (!file_exists($path)) {
            return;
        }

        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data)) {
            return;
        }

        $roleMap = [
            'Администратор' => 'admin',
            'Преподаватель' => 'teacher',
            'Студент' => 'student',
        ];

        $userMap = [];
        foreach (($data['users'] ?? []) as $rawUser) {
            $user = User::create([
                'name' => $rawUser['name'] ?? 'Без имени',
                'email' => $rawUser['email'] ?? uniqid('user_').'@top.local',
                'password' => Hash::make($rawUser['password'] ?? 'password'),
                'role' => $roleMap[$rawUser['role'] ?? 'Студент'] ?? 'student',
            ]);
            $userMap[$rawUser['id']] = $user->id;
        }

        foreach (($data['courses'] ?? []) as $course) {
            Course::create([
                'id' => $course['id'],
                'title' => $course['title'],
                'description' => $course['description'] ?? null,
                'duration' => $course['duration'] ?? null,
                'level' => $course['level'] ?? null,
                'tags' => $course['tags'] ?? null,
            ]);
        }

        foreach (($data['groups'] ?? []) as $group) {
            Group::create([
                'id' => $group['id'],
                'name' => $group['name'],
                'course_id' => $group['courseId'],
                'teacher_id' => $userMap[$group['teacherId']] ?? null,
                'description' => $group['description'] ?? null,
            ]);
        }

        foreach (($data['schedule'] ?? []) as $schedule) {
            Schedule::create([
                'id' => $schedule['id'],
                'course_id' => $schedule['courseId'],
                'group_id' => $schedule['groupId'],
                'weekday' => $schedule['weekday'],
                'time' => $schedule['time'],
                'room' => $schedule['room'],
            ]);
        }

        foreach (($data['user_groups'] ?? []) as $pivot) {
            $userId = $userMap[$pivot['userId']] ?? null;
            if (!$userId || ($pivot['roleInGroup'] ?? '') !== 'student') {
                continue;
            }

            $group = Group::find($pivot['groupId']);
            if ($group) {
                $group->students()->syncWithoutDetaching([$userId]);
            }
        }

        foreach (($data['feedback'] ?? []) as $feedback) {
            Feedback::create([
                'name' => $feedback['name'] ?? null,
                'email' => $feedback['email'] ?? null,
                'topic' => $feedback['topic'] ?? null,
                'course' => $feedback['course'] ?? null,
                'message' => $feedback['message'] ?? '',
                'created_at' => $feedback['timestamp'] ?? now(),
            ]);
        }
    }
}
