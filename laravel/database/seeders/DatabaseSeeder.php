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
        $data = [];
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
        }

        if (!is_array($data) || empty($data)) {
            $data = [
                'users' => [
                    ['id' => 'u-admin', 'name' => 'Администратор', 'email' => 'admin@top.local', 'role' => 'Администратор', 'password' => 'admin'],
                    ['id' => 'u-teacher', 'name' => 'Преподаватель', 'email' => 'teacher1@top.local', 'role' => 'Преподаватель', 'password' => 'teacher1'],
                    ['id' => 'u-student', 'name' => 'Студент', 'email' => 'student1@top.local', 'role' => 'Студент', 'password' => 'student1'],
                ],
                'courses' => [
                    [
                        'id' => 'web-basics',
                        'title' => 'Основы веб-разработки',
                        'description' => 'HTML, CSS и основы JavaScript.',
                        'duration' => '2 месяца',
                        'level' => 'начинающий',
                    ],
                ],
                'groups' => [
                    [
                        'id' => 'g-web-101',
                        'name' => 'WEB-101',
                        'courseId' => 'web-basics',
                        'teacherId' => 'u-teacher',
                        'description' => 'Группа для начинающих',
                    ],
                ],
                'schedule' => [
                    [
                        'id' => 'sch-001',
                        'courseId' => 'web-basics',
                        'groupId' => 'g-web-101',
                        'weekday' => 'Пн',
                        'time' => '18:00',
                        'room' => 'Ауд. 1',
                    ],
                ],
                'user_groups' => [
                    [
                        'userId' => 'u-student',
                        'groupId' => 'g-web-101',
                        'roleInGroup' => 'student',
                    ],
                ],
                'feedback' => [],
            ];
        }

        $roleMap = [
            'Администратор' => 'admin',
            'Преподаватель' => 'teacher',
            'Студент' => 'student',
        ];

        $userMap = [];
        foreach (($data['users'] ?? []) as $rawUser) {
            $user = User::updateOrCreate(
                ['email' => $rawUser['email'] ?? uniqid('user_').'@top.local'],
                [
                    'name' => $rawUser['name'] ?? 'Без имени',
                    'password' => Hash::make($rawUser['password'] ?? 'password'),
                    'role' => $roleMap[$rawUser['role'] ?? 'Студент'] ?? 'student',
                ]
            );
            $userMap[$rawUser['id']] = $user->id;
        }

        foreach (($data['courses'] ?? []) as $course) {
            Course::updateOrCreate(
                ['id' => $course['id']],
                [
                    'title' => $course['title'],
                    'description' => $course['description'] ?? null,
                    'duration' => $course['duration'] ?? null,
                    'level' => $course['level'] ?? null,
                    'tags' => $course['tags'] ?? null,
                ]
            );
        }

        foreach (($data['groups'] ?? []) as $group) {
            Group::updateOrCreate(
                ['id' => $group['id']],
                [
                    'name' => $group['name'],
                    'course_id' => $group['courseId'],
                    'teacher_id' => $userMap[$group['teacherId']] ?? null,
                    'description' => $group['description'] ?? null,
                ]
            );
        }

        foreach (($data['schedule'] ?? []) as $schedule) {
            Schedule::updateOrCreate(
                ['id' => $schedule['id']],
                [
                    'course_id' => $schedule['courseId'],
                    'group_id' => $schedule['groupId'],
                    'weekday' => $schedule['weekday'],
                    'time' => $schedule['time'],
                    'room' => $schedule['room'],
                ]
            );
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
            Feedback::updateOrCreate(
                [
                    'email' => $feedback['email'] ?? null,
                    'message' => $feedback['message'] ?? '',
                ],
                [
                    'name' => $feedback['name'] ?? null,
                    'topic' => $feedback['topic'] ?? null,
                    'course' => $feedback['course'] ?? null,
                    'created_at' => $feedback['timestamp'] ?? now(),
                ]
            );
        }
    }
}
