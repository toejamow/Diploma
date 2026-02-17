<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow($userId)
    {
        $user = User::findOrFail($userId);
        Auth::user()->following()->syncWithoutDetaching([$user->id]);

        return response()->json(['message' => 'Вы подписались']);
    }

    public function unfollow($userId)
    {
        $user = User::findOrFail($userId);
        Auth::user()->following()->detach($user->id);

        return response()->json(['message' => 'Вы отписались']);
    }

    public function userProfile(Request $request, $userId)
    {
        $user = User::with([])->findOrFail($userId);
        $authUser = auth()->user();

        $isFollowing = false;

        if ($authUser) {
            $isFollowing = $authUser
                ->following()
                ->where('subscribed_to_id', $user->id)
                ->exists();
        }

        $tasksQuery = $user->tasks()->with('goals', 'category');

        if ($request->filled('status')) {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $tasksQuery->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $tasksQuery->where('title', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'asc');

        $tasks = $tasksQuery->orderBy($sortBy, $sortOrder)->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_following' => $isFollowing,
            ],
            'tasks' => $tasks,
        ]);
    }

    public function getFollowsAndFollowers()
    {
        $user = auth()->user();

        // Получаем ID всех, на кого подписан пользователь
        $followingIds = $user->following()->pluck('users.id')->toArray();

        // Подписки (люди, на кого подписан)
        $following = $user->following()
            ->select('users.id', 'users.name', 'users.email')
            ->withCount(['tasks as notesCount'])
            ->get()
            ->map(function ($f) {
                $f->is_following = true;
                return $f;
            });

        // Подписчики (люди, которые подписаны на пользователя)
        $followers = $user->followers()
            ->select('users.id', 'users.name', 'users.email')
            ->withCount(['tasks as notesCount'])
            ->get()
            ->map(function ($f) use ($followingIds) {
                $f->is_following = in_array($f->id, $followingIds);
                return $f;
            });

        return response()->json([
            'following' => $following,
            'followers' => $followers,
            'following_count' => $following->count(),
            'followers_count' => $followers->count(),
        ]);
    }



}
