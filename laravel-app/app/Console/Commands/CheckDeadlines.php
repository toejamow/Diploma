<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use Carbon\Carbon;
Carbon::setLocale('ru');

class CheckDeadlines extends Command
{
    protected $signature = 'notifications:check-deadlines';
    protected $description = 'Check tasks deadlines and generate notifications';

    public function handle()
    {
        $tasks = Task::with('user')->get();
        $now = now();

        foreach ($tasks as $task) {
            if (!$task->deadline) {
                continue;
            }

            $due = Carbon::parse($task->deadline);
            $hoursToDeadline = $now->diffInHours($due, false);
            $userId = $task->user_id;

            $isCompleted = $task->status == '1';
            $isOverdue = $due->isPast() && !$isCompleted;
            
            // 1. Просрочена
            if (!$isCompleted && $due->isPast()) {
                $this->createNotificationOnce($userId, $task->id, 1  );
            }
            // 2. Срок подходит (<= 24ч), задача ещё не выполнена
            if (!$isCompleted && !$isOverdue && $hoursToDeadline <= 24 && $hoursToDeadline > 0) {
                $this->createNotificationOnce($userId, $task->id, 2 );
            }

            // 3. Завершена, но дедлайн уже близко (<= 24ч)
            if ($isCompleted && $hoursToDeadline <= 24 && $hoursToDeadline >= 0) {
                $this->createNotificationOnce($userId, $task->id, 3 );
            }
        }

        $this->info('Deadline notifications processed.');
    }

    protected function createNotificationOnce($userId, $taskId, $type_id)
    {
        $exists = Notification::where('user_id', $userId)
            ->where('task_id', $taskId)
            ->where('type_id', $type_id)
            ->exists();

        if (!$exists) {
            Notification::create([
                'user_id' => $userId,
                'task_id' => $taskId,
                'type_id' => $type_id,
            ]);
        }
    }
}

