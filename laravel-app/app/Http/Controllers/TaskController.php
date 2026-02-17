<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
Carbon::setLocale('ru');

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->tasks()->with('goals', 'category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSortFields = ['created_at', 'title'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortField, $sortOrder);

        $tasks = $query->get();

        return response()->json([
            'data' => $tasks,
            'total' => Task::where('user_id', auth()->id())->count()
        ]);

    }

    public function show($id)
    {
        $note = Task::with('goals', 'category')->find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        return response()->json(['data' => $note]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'deadline' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'goals' => 'nullable|array|max:10',
            'goals.*.description' => 'required|string|max:1000',
        ]);

        $task = $request->user()->tasks()->create([
            'title' => $validated['title'],
            'category_id' => $request->input('category_id'),
            'deadline' => $validated['deadline'],
            'created_at' => now()->toDateString()
        ]);

        if (!empty($validated['goals'])) {
            foreach ($validated['goals'] as $goalData) {
                $task->goals()->create([
                    'description' => $goalData['description']
                ]);
            }
        }

        return response()->json([
            'message' => 'Заметка успешно создана',
            'task' => $task->load('goals'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'deadline' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'goals' => 'nullable|array|max:10',
            'goals.*.id' => 'nullable|integer|exists:goals,id',
            'goals.*.description' => 'required|string|max:1000',
            'goals.*.is_completed' => 'nullable|boolean'
        ]);

        $task = Task::with('goals')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Заметка не найдена или не принадлежит пользователю'], 404);
        }

        $newDeadline = Carbon::parse($validated['deadline'])->toDateString();
        $currentStatus = $task->status;

        // По умолчанию статус остаётся прежним
        $newStatus = $currentStatus;

        // Если был просрочен и срок сдвинули на будущее — статус = "В процессе"
        if ($currentStatus == '3' && Carbon::parse($newDeadline)->isFuture()) {
            $newStatus = '2';
        }

        $task->update([
            'title' => $validated['title'],
            'deadline' => $newDeadline,
            'category_id' => $request->input('category_id'),
            'status' => $newStatus
        ]);

        // Обработка целей
        if (!empty($validated['goals'])) {
            $incomingGoals = collect($validated['goals']);

            $existingGoalIds = $task->goals?->pluck('id')->map(fn($id) => (string) $id)->toArray() ?? [];
            $submittedGoalIds = $incomingGoals->pluck('id')->filter()->map(fn($id) => (string) $id)->toArray();

            $goalsToDelete = array_diff($existingGoalIds, $submittedGoalIds);
            if (!empty($goalsToDelete)) {
                $task->goals()->whereIn('id', $goalsToDelete)->delete();
            }

            foreach ($incomingGoals as $goalData) {
                $goalData['is_completed'] = $goalData['is_completed'] ?? false;

                if (!empty($goalData['id'])) {
                    $goal = $task->goals()->where('id', $goalData['id'])->first();
                    if ($goal) {
                        $goal->update([
                            'description' => $goalData['description'],
                            'is_completed' => $goalData['is_completed'],
                        ]);
                        continue;
                    }
                }

                $task->goals()->create([
                    'description' => $goalData['description'],
                    'is_completed' => $goalData['is_completed'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Заметка успешно обновлена',
            'task' => $task->fresh('goals'),
        ]);
    }

    public function updateGoal(Request $request, $id)
    {
        $goal = Goal::find($id);

        if (!$goal) {
            return response()->json(['message' => 'Задача не найдена'], 404);
        }

        $request->validate([
            'is_completed' => 'nullable|boolean',
        ]);

        $goal->is_completed = $request->input('is_completed');
        $goal->save();

        // Получаем связанную заметку
        $task = $goal->task;

        if ($task) {
            $totalGoals = $task->goals()->count();
            $completedGoals = $task->goals()->where('is_completed', true)->count();

            if ($completedGoals === $totalGoals && $totalGoals > 0) {
                $task->status = '1'; // Выполнена
            } elseif ($completedGoals > 0) {
                $task->status = '2'; // В процессе
            } else {
                $task->status = '0'; // Новая
            }

            $task->save();
        }

        return response()->json([
            'message' => 'Задача успешно обновлена',
            'goal' => $goal
        ], 200);
    }

    public function destroy($id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Заметка не найдена или не принадлежит пользователю'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Заметка успешно удалена'
        ], 200);
    }

    public function destroyGoal($id)
    {
        $goal = Goal::find($id);

        if (!$goal || $goal->task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Цель не найдена или не принадлежит вам'], 404);
        }

        $goal->delete();

        return response()->json(['message' => 'Цель удалена']);
    }
    public function generateTasks(Request $request)
    {
        $request->validate([
            'goal' => 'required|string|max:255',
        ]);
        $goal = $request->input('goal');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.ai.api_key'),
            'X-Title' => 'GoalPlannerApp',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'openai/gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Ты помощник по планированию целей. Разбивай цели на 5 конкретных задач. Отделяй каждую задачу "---". Без цифр, точек, скобок.'],
                        ['role' => 'user', 'content' => "Моя цель: $goal"],
                    ],
                ]);
        if ($response->failed()) {
            return response()->json([
                'error' => 'Ошибка при обращении к ИИ',
                'details' => $response->body(),
            ], 500);
        }

        $content = $response['choices'][0]['message']['content'] ?? '';
        $lines = collect(explode("\n", $content))
            ->map(fn($line) => trim(preg_replace('/^[-•\\d\\.\\s]*/', '', $line)))
            ->filter()
            ->values();

        return response()->json([
            'tasks' => $lines,
        ]);
    }

    public function downloadStatistics()
    {
        $user = Auth::user();
        $notes = Task::with('goals')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $noteCount = $notes->count();
        $goalCount = 0;
        $completedGoals = 0;

        // Подсчет по статусам
        $statusCounts = [
            '0' => 0, // Новая
            '1' => 0, // Выполнена
            '2' => 0, // В процессе
            '3' => 0  // Просрочена
        ];

        foreach ($notes as $note) {
            $status = (string) $note->status;
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }

            foreach ($note->goals as $goal) {
                $isCompleted = is_array($goal) ? ($goal['is_completed'] ?? 0) : ($goal->is_completed ?? 0);
                $goalCount++;
                if ((int) $isCompleted === 1) {
                    $completedGoals++;
                }
            }
        }

        $completionPercentage = $goalCount > 0
            ? round(($completedGoals / $goalCount) * 100, 2)
            : 0;

        $oldestNote = $notes->sortBy('created_at')->first();
        $newestNote = $notes->sortByDesc('created_at')->first();

        // Первая диаграмма — по задачам
        $taskChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Выполнено', 'Не выполнено'],
                'datasets' => [
                    [
                        'data' => [$completedGoals, $goalCount - $completedGoals],
                        'backgroundColor' => ['#22c55e', '#d93232'],
                        'borderWidth' => 2,
                    ]
                ]
            ],
            'options' => [
                'cutout' => '70%',
                'layout' => ['padding' => 30],
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                    'datalabels' => ['color' => '#000', 'font' => ['weight' => 'bold', 'size' => 16]],
                    'doughnutlabel' => [
                        'labels' => [
                            ['text' => $goalCount, 'font' => ['size' => 26, 'weight' => 'bold'], 'color' => '#000'],
                            ['text' => 'Задач всего', 'font' => ['size' => 14], 'color' => '#666']
                        ]
                    ]
                ]
            ]
        ];

        $taskChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($taskChartConfig)) . '&plugins=doughnutlabel,datalabels&width=600&height=500';

        // Вторая диаграмма — по статусам
        $statusLabels = ['Новая', 'Выполнена', 'В процессе', 'Просрочена'];
        $statusColors = ['#f0f0f0', '#22c55e', '#3b82f6', '#f59e0b'];
        $statusChartConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $statusLabels,
                'datasets' => [
                    [
                        'data' => array_values($statusCounts),
                        'backgroundColor' => $statusColors,
                        'borderWidth' => 2,
                    ]
                ]
            ],
            'options' => [
                'cutout' => '70%',
                'layout' => ['padding' => 30],
                'plugins' => [
                    'legend' => ['position' => 'bottom'],
                    'datalabels' => ['color' => '#000', 'font' => ['weight' => 'bold', 'size' => 16]],
                    'doughnutlabel' => [
                        'labels' => [
                            ['text' => $noteCount, 'font' => ['size' => 26, 'weight' => 'bold'], 'color' => '#000'],
                            ['text' => 'Заметок всего', 'font' => ['size' => 14], 'color' => '#666']
                        ]
                    ]
                ]
            ]
        ];

        $statusChartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($statusChartConfig)) . '&plugins=doughnutlabel,datalabels&width=600&height=500';

        $data = [
            'name' => $user->name,
            'date' => Carbon::parse(now())->translatedFormat('d F Y H:i'),
            'note_count' => $noteCount,
            'goal_count' => $goalCount,
            'completed_goals' => $completedGoals,
            'completion_percentage' => $completionPercentage,
            'oldest_note' => $oldestNote,
            'newest_note' => $newestNote,
            'oldest_note_date' => $oldestNote?->created_at ? Carbon::parse($oldestNote->created_at)->translatedFormat('d F Y') : '—',
            'newest_note_date' => $newestNote?->created_at ? Carbon::parse($newestNote->created_at)->translatedFormat('d F Y') : '—',
            'taskChartUrl' => $taskChartUrl,
            'statusChartUrl' => $statusChartUrl
        ];

        $pdf = PDF::loadView('statistics.pdf', $data);
        $pdf->setOptions(['isRemoteEnabled' => true]);
        return $pdf->download('statistics.pdf');
    }

    public function getStatistics(Request $request)
    {
        $user = Auth::user();

        // Период: по умолчанию 7 дней
        $from = Carbon::parse($request->input('from', now()->subDays(7)))->startOfDay();
        $to = Carbon::parse($request->input('to', now()))->endOfDay();

        // Заметки за период
        $notes = Task::with('goals')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $noteCount = $notes->count();
        $goalCount = 0;
        $completedGoals = 0;

        $oldestNote = $notes->sortBy('created_at')->first();
        $newestNote = $notes->sortByDesc('created_at')->first();

        $statusCounts = [
            '0' => 0, // Новая
            '1' => 0, // Выполнена
            '2' => 0, // В процессе
            '3' => 0  // Просрочена
        ];

        foreach ($notes as $note) {
            $status = (string) $note->status;
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }

            foreach ($note->goals as $goal) {
                $goalCount++;
                if ((int) $goal->is_completed === 1) {
                    $completedGoals++;
                }
            }
        }

        return response()->json([
            'note_count' => $noteCount,
            'goal_count' => $goalCount,
            'completed_goals' => $completedGoals,
            'completion_percentage' => $goalCount > 0 ? round(($completedGoals / $goalCount) * 100, 2) : 0,
            'status_counts' => $statusCounts,
            'oldest_note_date' => $oldestNote?->created_at ? Carbon::parse($oldestNote->created_at)->translatedFormat('d F Y') : '—',
            'newest_note_date' => $newestNote?->created_at ? Carbon::parse($newestNote->created_at)->translatedFormat('d F Y') : '—',
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }


    public function public($token)
    {
        $task = Task::with('goals', 'user:id,name,email')->where('public_token', $token)->first();

        if (!$task) {
            return response()->json(['message' => 'Заметка не найдена'], 404);
        }

        return response()->json(['data' => $task]);
    }

    public function makePublic($id)
    {
        $user = auth()->user();
        $task = Task::where('id', $id)->where('user_id', $user->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Заметка не найдена или не принадлежит вам'], 404);
        }

        // Если уже публичная — просто возвращаем токен
        if ($task->public_token) {
            return response()->json([
                'message' => 'Заметка уже является публичной',
                'public_url' => 'http://localhost:5173/shared/' . $task->public_token
            ]);
        }

        // Генерация токена и сохранение
        $task->public_token = Str::uuid();
        $task->save();

        return response()->json([
            'message' => 'Заметка теперь публичная',
            'public_url' => 'http://localhost:5173/shared/' . $task->public_token
        ]);
    }

    public function revokeShare($id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Заметка не найдена'], 404);
        }

        $task->public_token = null;
        $task->save();

        return response()->json([
            'message' => 'Публичная ссылка удалена',
            'task' => $task
        ]);
    }
}