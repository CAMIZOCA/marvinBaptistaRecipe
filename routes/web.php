<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeListController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;

// Sitemap & robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Recipe listing & categories
Route::get('/recetas', [RecipeListController::class, 'index'])->name('recipes.index');
Route::get('/recetas/{category}/{subcategory}', [SubcategoryController::class, 'show'])->name('subcategory.show');
Route::get('/recetas/{category}', [CategoryController::class, 'show'])->name('category.show');

// Ingredient index pages
Route::get('/ingrediente/{ingredient}', [IngredientController::class, 'show'])->name('ingredient.show');

// Store (Amazon books)
Route::get('/tienda', [StoreController::class, 'index'])->name('store.index');
Route::get('/tienda/{book}', [StoreController::class, 'show'])->name('store.show');

// Static pages
Route::get('/pagina/{page}', [PageController::class, 'show'])->name('page.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Admin routes — loaded BEFORE the /{slug} catch-all to avoid conflict
require __DIR__.'/admin.php';

// Recipe show — must be LAST (catch-all by slug)
Route::get('/{slug}', [RecipeController::class, 'show'])->name('recipe.show')
    ->where('slug', '[a-z0-9\-]+');
