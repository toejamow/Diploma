<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    // Получить все уведомления текущего пользователя
    public function index()
    {
        $user = Auth::id();
        $notifications = Notification::where('user_id', $user)->with('task')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // Получить количество непрочитанных
    public function unreadCount()
    {
        $user = Auth::id();
        $count = Notification::where('user_id', $user)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread' => $count]);
    }

    // Отметить все как прочитанные
    public function markAllRead()
    {
        $user = Auth::id();
        Notification::where('user_id', $user)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Все уведомления отмечены как прочитанные']);
    }

    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->delete();

        return response()->json(['message' => 'Уведомление удалено']);
    }

}
