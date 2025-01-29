<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{  
    public function indexForAnalyst()
    {
        $user = auth('admin')->user();

        $notifications = Notification::where('assignee_id', $user->id_admins)
            ->where('is_read', false)
            ->with(['laporan', 'assigner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(), 
        ]);
    }

    public function roleBasedNotifications()
    {
        $user = auth('admin')->user();
        $role = $user->role;

        $notifications = Notification::where('role', $role)
            ->where('is_read', false)
            ->with(['laporan', 'assigner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $notifications->count(), 
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:notifications,id',
        ]);

        $notification = Notification::find($request->id);
        $user = auth('admin')->user();

        if ($notification && ($notification->assignee_id == $user->id_admins || $notification->role == $user->role)) {
            $notification->is_read = true;
            $notification->save();

            return response()->json(['success' => true, 'message' => 'Notifikasi berhasil ditandai sebagai dibaca']);
        }

        return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan atau tidak berhak'], 403);
    }
}