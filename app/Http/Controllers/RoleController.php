<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Role::class, 'role');
    }
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        $roles = Role::with('users')->get();
        return response()->json($roles);
    }
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);
        $role = Role::create($validated);
        return response()->json($role, 201);
    }
    public function show($id)
    {
        $role = Role::with('users')->findOrFail($id);
        $this->authorize('view', $role);
        return response()->json($role);
    }
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('update', $role);
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $id,
            'description' => 'nullable|string',
        ]);
        $role->update($validated);
        return response()->json($role);
    }
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('delete', $role);
        $role->delete();
        return response()->json(['message' => 'Role dihapus']);
    }
    public function assignUser(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('update', $role);
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        $user = User::findOrFail($validated['user_id']);
        $user->roles()->syncWithoutDetaching([$role->id]);
        return response()->json(['message' => 'User ditambahkan ke role']);
    }
}