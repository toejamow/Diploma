<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Str;


class Task extends Model
{

    public function goals()
    {
        return $this->hasMany(Goal::class, 'task_id');
    }

    protected $fillable = [
        'title',
        'user_id',
        'deadline',
        'goals',
        'status',
        'created_at',
        'public_token',
        'category_id', 
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function makePublic()
    {
        $this->public_token = Str::uuid();
        $this->save();
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }





}
