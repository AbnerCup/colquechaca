<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Ventas', 'tipo' => 'ingreso'],
            ['nombre' => 'Servicios Prestados', 'tipo' => 'ingreso'],
            ['nombre' => 'Alquiler', 'tipo' => 'egreso'],
            ['nombre' => 'Sueldos', 'tipo' => 'egreso'],
            ['nombre' => 'Servicios Básicos', 'tipo' => 'egreso'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
