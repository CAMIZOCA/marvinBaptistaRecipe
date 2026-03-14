@extends('admin.layouts.app')

@section('page-title', isset($user) ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
@php
    $isEdit = isset($user) && $user->exists;
    $formAction = $isEdit ? '/admin/usuarios/'.$user->id : '/admin/usuarios';
@endphp

<div class="p-6 space-y-6 max-w-2xl">

    <div class="flex items-center gap-3">
        <a href="/admin/usuarios"
           class="p-2 text-zinc-400 hover:text-zinc-200 hover:bg-zinc-700 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-100">
            {{ $isEdit ? 'Editar: '.$user->name : 'Nuevo Usuario' }}
        </h1>
    </div>

    @if($errors->any())
    <div class="p-4 bg-red-900/50 border border-red-700 rounded-xl text-red-300 text-sm space-y-1">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Información de Cuenta</h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-300 mb-1.5">Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                           placeholder="Nombre Apellido"
                           class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                           required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-300 mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                           placeholder="usuario@ejemplo.com"
                           class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                           required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-300 mb-1.5">
                        Contraseña {{ $isEdit ? '(dejar vacío para no cambiar)' : '*' }}
                    </label>
                    <input type="password" name="password"
                           placeholder="{{ $isEdit ? '••••••••' : 'Mínimo 8 caracteres' }}"
                           class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                           {{ $isEdit ? '' : 'required' }}
                           minlength="8">
                </div>

                @if(!$isEdit)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-zinc-300 mb-1.5">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Repite la contraseña"
                           class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                           required>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Rol y Permisos</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Rol *</label>
                <select name="role"
                        class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>Usuario</option>
                    <option value="editor" {{ old('role', $user->role ?? '') === 'editor' ? 'selected' : '' }}>Editor</option>
                    <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-zinc-300">Cuenta activa</label>
                    <p class="text-xs text-zinc-500 mt-0.5">El usuario puede iniciar sesión</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-zinc-600 peer-focus:ring-2 peer-focus:ring-amber-500 rounded-full peer peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
            </div>
        </div>

        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-5 space-y-4">
            <h2 class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Perfil</h2>

            <div>
                <label class="block text-sm font-medium text-zinc-300 mb-1.5">Bio</label>
                <textarea name="bio" rows="3"
                          placeholder="Breve descripción del usuario..."
                          class="w-full px-4 py-2.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-xl placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-xl font-bold text-sm transition-colors">
                {{ $isEdit ? 'Actualizar Usuario' : 'Crear Usuario' }}
            </button>
            <a href="/admin/usuarios"
               class="px-6 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-300 rounded-xl text-sm transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
