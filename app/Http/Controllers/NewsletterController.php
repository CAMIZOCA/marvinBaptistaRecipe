<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Por favor introduce tu correo electrónico.',
            'email.email'    => 'El correo electrónico no es válido.',
        ]);

        $email = strtolower(trim($request->input('email')));

        $exists = DB::table('newsletter_subscribers')->where('email', $email)->exists();

        if ($exists) {
            return back()->with('newsletter_info', '¡Ya estás suscrito con ese correo! 🎉');
        }

        DB::table('newsletter_subscribers')->insert([
            'email'      => $email,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('newsletter_success', '¡Suscripción confirmada! Pronto recibirás las mejores recetas. 🍽️');
    }
}
