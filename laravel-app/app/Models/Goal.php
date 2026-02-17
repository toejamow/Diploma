<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Goal extends Model
{

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }



    public $fillable = [
        'description',
        'task_id',
        'is_completed'
    ];

    public $timestamps = false;
}
