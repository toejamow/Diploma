<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

     protected $signature = 'tasks:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить статус задач на "Просрочена", если срок сдачи истёк';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $updated = Task::whereIn('status', ['0', '2'])
        ->whereDate('deadline', '<', Carbon::now()->toDateString())
        ->update(['status' => '3']); // 3 — Просрочена

    $this->info("Обновлено $updated просроченных задач.");
}


}
