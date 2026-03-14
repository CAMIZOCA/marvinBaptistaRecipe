<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $user = new User(['role' => 'editor', 'is_active' => true]);
        return view('admin.users.edit', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:super_admin,admin,editor'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active');
        User::create($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:super_admin,admin,editor'],
            'bio' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $user->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
