<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Task;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed'
    ];
    public $timestamps = false;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function following() {
        return $this->belongsToMany(User::class, 'following_list', 'subscriber_id', 'subscribed_to_id');
    }

    public function followers() {
        return $this->belongsToMany(User::class, 'following_list', 'subscribed_to_id', 'subscriber_id');

    }

}
