<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $roles = [
            'admin',
            'branch_manager',
            'teller',
            'cashier',
            'vault_operator',
            'auditor',
            'hr_head',
        ];

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([
                'admin',
                'branch_manager',
                'teller',
                'cashier',
                'vault_operator',
                'auditor',
                'hr_head',
            ])],
        ]);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([
                'admin',
                'branch_manager',
                'teller',
                'cashier',
                'vault_operator',
                'auditor',
                'hr_head',
            ])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $isSelf = (int) $request->user()->id === (int) $user->id;

        if ($isSelf && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User account updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('error', 'You cannot delete your own account from admin panel.');
        }

        if ($user->role === 'admin') {
            $adminsCount = User::where('role', 'admin')->count();
            if ($adminsCount <= 1) {
                return back()->with('error', 'Cannot delete the last admin account.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted successfully.');
    }
}
