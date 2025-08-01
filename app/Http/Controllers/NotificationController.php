<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Notification::class, 'notification');
    }
    public function index()
    {
        $this->authorize('viewAny', Notification::class);
        $notifications = Notification::with('user')->latest()->paginate(20);
        return response()->json($notifications);
    }
    public function store(Request $request)
    {
        $this->authorize('create', Notification::class);
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);
        $notification = Notification::create($validated);
        // Simulasi push notifikasi
        // event(new NotificationSent($notification));
        return response()->json($notification, 201);
    }
    public function show($id)
    {
        $notification = Notification::with('user')->findOrFail($id);
        $this->authorize('view', $notification);
        return response()->json($notification);
    }
    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('update', $notification);
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'body' => 'sometimes|string',
        ]);
        $notification->update($validated);
        return response()->json($notification);
    }
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('delete', $notification);
        $notification->delete();
        return response()->json(['message' => 'Notifikasi dihapus']);
    }
}