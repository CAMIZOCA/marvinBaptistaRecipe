<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super admin user
        User::create([
            'name' => 'Marvin Baptista',
            'email' => 'admin@marvinbaptista.com',
            'password' => Hash::make('Admin2025!'),
            'role' => 'super_admin',
            'is_active' => true,
            'bio' => 'Chef y escritor gastronómico especializado en cocina latinoamericana y mediterránea.',
        ]);

        // Default categories
        $categories = [
            ['name' => 'Recetas Ecuatorianas', 'children' => ['Sopa y Caldos', 'Mariscos', 'Carnes', 'Postres Ecuatorianos']],
            ['name' => 'Recetas Latinoamericanas', 'children' => ['Mexicana', 'Peruana', 'Colombiana', 'Argentina']],
            ['name' => 'Recetas Mediterráneas', 'children' => ['Española', 'Italiana', 'Griega']],
            ['name' => 'Desayunos', 'children' => []],
            ['name' => 'Ensaladas', 'children' => []],
            ['name' => 'Postres', 'children' => ['Tortas', 'Helados', 'Galletas']],
            ['name' => 'Bebidas', 'children' => ['Jugos', 'Cócteles', 'Aguas']],
        ];

        foreach ($categories as $i => $catData) {
            $parent = Category::create([
                'name' => $catData['name'],
                'sort_order' => $i + 1,
            ]);

            foreach ($catData['children'] as $j => $childName) {
                Category::create([
                    'name' => $childName,
                    'parent_id' => $parent->id,
                    'sort_order' => $j + 1,
                ]);
            }
        }

        // Default static pages
        $pages = [
            ['slug' => 'politica-de-privacidad', 'title' => 'Política de Privacidad', 'is_published' => true],
            ['slug' => 'politica-de-cookies', 'title' => 'Política de Cookies', 'is_published' => true],
            ['slug' => 'terminos-y-condiciones', 'title' => 'Términos y Condiciones', 'is_published' => true],
            ['slug' => 'sobre-mi', 'title' => 'Sobre Mí', 'is_published' => true],
            ['slug' => 'contacto', 'title' => 'Contacto', 'is_published' => true],
            ['slug' => 'aviso-legal', 'title' => 'Aviso Legal', 'is_published' => true],
        ];

        foreach ($pages as $pageData) {
            Page::create([
                'title' => $pageData['title'],
                'slug' => $pageData['slug'],
                'content' => '<p>Contenido pendiente de edición.</p>',
                'is_published' => $pageData['is_published'],
            ]);
        }

        // Default settings
        Setting::setMany([
            'site_name' => 'Marvin Baptista',
            'site_tagline' => 'Sabores de Latinoamérica y el Mediterráneo',
            'author_name' => 'Marvin Baptista',
            'author_bio' => 'Chef apasionado por la cocina latinoamericana y mediterránea.',
        ], 'general');

        Setting::setMany([
            'affiliate_tag' => 'marvinbaptista-20',
            'default_country' => 'EC',
        ], 'amazon');
    }
}
