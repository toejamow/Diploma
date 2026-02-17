<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'type_id'
    ];

    public function type() {
        return $this->belongsTo(Type::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function task() {
        return $this->belongsTo(Task::class);
    }

    public $timestamps = false;
}
