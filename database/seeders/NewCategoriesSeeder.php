<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class NewCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Añadir nuevos hijos a "Recetas Latinoamericanas" (ya existe)
        $latinoamericanas = Category::where('name', 'Recetas Latinoamericanas')->first();

        if ($latinoamericanas) {
            $nuevasCocinas = [
                'Boliviana',
                'Brasileña',
                'Chilena',
                'Costarricense',
                'Cubana',
                'Dominicana',
                'Guatemalteca',
                'Haitiana',
                'Hondureña',
                'Nicaragüense',
                'Panameña',
                'Paraguaya',
                'Puertorriqueña',
                'Salvadoreña',
                'Uruguaya',
            ];

            $currentMax = Category::where('parent_id', $latinoamericanas->id)->max('sort_order') ?? 4;

            foreach ($nuevasCocinas as $i => $nombre) {
                Category::firstOrCreate(
                    ['name' => $nombre, 'parent_id' => $latinoamericanas->id],
                    ['sort_order' => $currentMax + $i + 1]
                );
            }
        }

        // 2. Nueva categoría padre: Recetas Asiáticas
        $asiaticas = Category::firstOrCreate(
            ['name' => 'Recetas Asiáticas', 'parent_id' => null],
            ['sort_order' => 8]
        );

        foreach (['China', 'Japonesa'] as $i => $nombre) {
            Category::firstOrCreate(
                ['name' => $nombre, 'parent_id' => $asiaticas->id],
                ['sort_order' => $i + 1]
            );
        }

        // 3. Nueva categoría padre: Recetas Europeas
        $europeas = Category::firstOrCreate(
            ['name' => 'Recetas Europeas', 'parent_id' => null],
            ['sort_order' => 9]
        );

        foreach (['Portuguesa', 'Francesa'] as $i => $nombre) {
            Category::firstOrCreate(
                ['name' => $nombre, 'parent_id' => $europeas->id],
                ['sort_order' => $i + 1]
            );
        }

        // 4. Nueva categoría padre: Otras Cocinas Internacionales
        $otras = Category::firstOrCreate(
            ['name' => 'Otras Cocinas Internacionales', 'parent_id' => null],
            ['sort_order' => 10]
        );

        foreach (['Judía', 'Medio Oriente', 'Mediterránea'] as $i => $nombre) {
            Category::firstOrCreate(
                ['name' => $nombre, 'parent_id' => $otras->id],
                ['sort_order' => $i + 1]
            );
        }

        // 5. Nueva categoría padre: Recetas Especiales
        $especiales = Category::firstOrCreate(
            ['name' => 'Recetas Especiales', 'parent_id' => null],
            ['sort_order' => 11]
        );

        Category::firstOrCreate(
            ['name' => 'Recetas para Diabetes', 'parent_id' => $especiales->id],
            ['sort_order' => 1]
        );

        $this->command->info('✓ Nuevas categorías insertadas correctamente.');
    }
}
