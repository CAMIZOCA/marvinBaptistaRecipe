@extends('admin.layouts.app')

@section('page-title', 'Usuarios')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-100">Usuarios</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ isset($users) ? $users->count() : 0 }} usuarios registrados</p>
        </div>
        <a href="/admin/usuarios/create"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-900/50 border border-emerald-700 rounded-xl text-emerald-300 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        @if(isset($users) && $users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Rol</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-400 uppercase tracking-wider">Activo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wider">Registro</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700/50">
                    @foreach($users as $user)
                    <tr class="hover:bg-zinc-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0"
                                     style="background: linear-gradient(135deg, #d97706, #b45309);">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-zinc-200">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-400">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $roleClasses = match($user->role ?? 'user') {
                                    'admin'  => 'bg-red-900/50 text-red-300 border-red-700/50',
                                    'editor' => 'bg-blue-900/50 text-blue-300 border-blue-700/50',
                                    default  => 'bg-zinc-700 text-zinc-400 border-zinc-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleClasses }}">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->is_active ?? true)
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                            @else
                            <span class="inline-block w-2 h-2 rounded-full bg-zinc-600"></span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-400">
                            {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/admin/usuarios/{{ $user->id }}/edit"
                                   class="p-1.5 text-zinc-500 hover:text-amber-400 transition-colors rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form method="POST" action="/admin/usuarios/{{ $user->id }}"
                                      onsubmit="return confirm('¿Eliminar usuario «{{ addslashes($user->name) }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-16">
            <h3 class="text-lg font-semibold text-zinc-300 mb-2">Sin usuarios</h3>
            <a href="/admin/usuarios/create"
               class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-zinc-900 rounded-lg text-sm font-bold transition-colors mt-4">
                Nuevo Usuario
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
