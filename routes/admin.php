<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IngredientAdminController;
use App\Http\Controllers\Admin\PageAdminController;
use App\Http\Controllers\Admin\RecipeAiController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\RecipeImportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Auth routes (no admin middleware)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Protected admin routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Recipes
    Route::prefix('recetas')->name('recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');
        Route::get('/crear', [RecipeController::class, 'create'])->name('create');
        Route::post('/', [RecipeController::class, 'store'])->name('store');
        Route::get('/{recipe}/editar', [RecipeController::class, 'edit'])->name('edit');
        Route::put('/{recipe}', [RecipeController::class, 'update'])->name('update');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
        Route::post('/{recipe}/toggle-published', [RecipeController::class, 'togglePublished'])->name('toggle-published');

        // AI Enhancement
        Route::post('/{recipe}/mejorar-ia', [RecipeAiController::class, 'enhance'])->name('ai.enhance');
        Route::post('/{recipe}/mejorar-ia/guardar', [RecipeAiController::class, 'saveEnhancement'])->name('ai.save');

        // CSV Import
        Route::get('/importar', [RecipeImportController::class, 'index'])->name('import.index');
        Route::post('/importar', [RecipeImportController::class, 'store'])->name('import.store');
        Route::get('/importar/progreso/{batch}', [RecipeImportController::class, 'progress'])->name('import.progress');
    });

    // Categories
    Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category']);

    // Tags
    Route::resource('etiquetas', TagController::class)->parameters(['etiquetas' => 'tag']);

    // Books
    Route::resource('libros', BookController::class)->parameters(['libros' => 'book']);

    // Ingredient Index
    Route::resource('ingredientes', IngredientAdminController::class)->parameters(['ingredientes' => 'ingredient']);

    // Static Pages
    Route::resource('paginas', PageAdminController::class)->parameters(['paginas' => 'page']);

    // Users (super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'user']);
    });

    // Settings
    Route::get('/ajustes', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/ajustes', [SettingController::class, 'update'])->name('settings.update');
});
