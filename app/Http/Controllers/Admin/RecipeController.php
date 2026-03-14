<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeFaq;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Recipe::with('categories', 'tags')
            ->withTrashed($request->boolean('trashed'));

        // Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            match($request->status) {
                'published' => $query->published(),
                'draft' => $query->draft(),
                'ai_pending' => $query->aiPending()->published(),
                default => null,
            };
        }
        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category));
        }
        if ($request->filled('country')) {
            $query->where('origin_country', $request->country);
        }

        $recipes = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::topLevel()->with('children')->ordered()->get();
        $countries = Recipe::whereNotNull('origin_country')->distinct()->pluck('origin_country')->sort();

        return view('admin.recipes.index', compact('recipes', 'categories', 'countries'));
    }

    public function create(): View
    {
        $recipe = new Recipe(['difficulty' => 'medium', 'servings_unit' => 'porciones', 'servings' => 4]);
        $categories = Category::topLevel()->with('children')->ordered()->get();
        $tags = Tag::orderBy('name')->get();
        $books = AmazonBook::where('is_active', true)->orderBy('title')->get();

        return view('admin.recipes.edit', compact('recipe', 'categories', 'tags', 'books'));
    }

    public function store(RecipeRequest $request): RedirectResponse
    {
        $recipe = DB::transaction(function () use ($request) {
            $data = $request->except(['categories', 'tags', 'ingredients', 'steps', 'faqs', 'primary_category']);
            $data['user_id'] = auth()->id();
            $data['is_published'] = $request->boolean('is_published');

            if ($data['is_published'] && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $recipe = Recipe::create($data);
            $this->syncRelations($recipe, $request);

            return $recipe;
        });

        return redirect()->route('admin.recipes.edit', $recipe)
            ->with('success', '¡Receta creada exitosamente!');
    }

    public function edit(Recipe $recipe): View
    {
        $recipe->load('ingredients', 'steps', 'faqs', 'categories', 'tags', 'books');
        $categories = Category::topLevel()->with('children')->ordered()->get();
        $tags = Tag::orderBy('name')->get();
        $books = AmazonBook::where('is_active', true)->orderBy('title')->get();

        return view('admin.recipes.edit', compact('recipe', 'categories', 'tags', 'books'));
    }

    public function update(RecipeRequest $request, Recipe $recipe): RedirectResponse
    {
        DB::transaction(function () use ($request, $recipe) {
            $data = $request->except(['categories', 'tags', 'ingredients', 'steps', 'faqs', 'primary_category']);
            $data['is_published'] = $request->boolean('is_published');

            if ($data['is_published'] && !$recipe->published_at && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $recipe->update($data);
            $this->syncRelations($recipe, $request);
        });

        // Invalidate cache
        Cache::tags(['recipes'])->flush();

        return redirect()->route('admin.recipes.edit', $recipe)
            ->with('success', '¡Receta actualizada exitosamente!');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Receta eliminada.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
        ]);

        $ids    = $request->input('ids');
        $action = $request->input('action');
        $count  = count($ids);

        switch ($action) {
            case 'delete':
                Recipe::whereIn('id', $ids)->delete();
                $message = "{$count} receta(s) eliminada(s) correctamente.";
                break;

            case 'publish':
                // Set published_at only on those that haven't been published before
                Recipe::whereIn('id', $ids)->whereNull('published_at')
                    ->update(['published_at' => now()]);
                Recipe::whereIn('id', $ids)->update(['is_published' => true]);
                $message = "{$count} receta(s) publicada(s).";
                break;

            case 'unpublish':
                Recipe::whereIn('id', $ids)->update(['is_published' => false]);
                $message = "{$count} receta(s) despublicada(s).";
                break;
        }

        // Preserve active filters when redirecting back
        return redirect()->route('admin.recipes.index', $request->only(['search', 'status', 'category_id', 'difficulty']))
            ->with('success', $message ?? 'Operación completada.');
    }

    public function togglePublished(Recipe $recipe): \Illuminate\Http\JsonResponse
    {
        $recipe->update([
            'is_published' => !$recipe->is_published,
            'published_at' => !$recipe->is_published ? now() : $recipe->published_at,
        ]);

        Cache::tags(['recipes'])->flush();

        return response()->json([
            'published' => $recipe->is_published,
            'message' => $recipe->is_published ? 'Receta publicada.' : 'Receta despublicada.',
        ]);
    }

    // Handle image upload
    public function uploadImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['image' => 'required|image|max:5120']);

        $path = $request->file('image')->store('recipes', 'public');

        return response()->json(['url' => Storage::url($path), 'path' => $path]);
    }

    private function syncRelations(Recipe $recipe, RecipeRequest $request): void
    {
        // Categories
        if ($request->has('categories')) {
            $categoryPivot = [];
            foreach ($request->categories as $catId) {
                $categoryPivot[$catId] = ['is_primary' => ($catId == $request->primary_category)];
            }
            $recipe->categories()->sync($categoryPivot);
        }

        // Tags
        if ($request->has('tags')) {
            $recipe->tags()->sync($request->tags ?? []);
        }

        // Ingredients (JSON array)
        if ($request->filled('ingredients')) {
            $ingredients = json_decode($request->ingredients, true) ?? [];
            $recipe->ingredients()->delete();
            foreach ($ingredients as $i => $ing) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'order_position' => $i + 1,
                    'amount' => $ing['amount'] ?? null,
                    'unit' => $ing['unit'] ?? null,
                    'ingredient_name' => $ing['name'] ?? '',
                    'ingredient_group' => $ing['group'] ?? null,
                    'notes' => $ing['notes'] ?? null,
                ]);
            }
        }

        // Steps (JSON array)
        if ($request->filled('steps')) {
            $steps = json_decode($request->steps, true) ?? [];
            $recipe->steps()->delete();
            foreach ($steps as $i => $step) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $i + 1,
                    'title' => $step['title'] ?? null,
                    'description' => $step['description'] ?? '',
                    'duration_minutes' => $step['duration'] ?? null,
                    'image' => $step['image'] ?? null,
                ]);
            }
        }

        // FAQs (JSON array)
        if ($request->filled('faqs')) {
            $faqs = json_decode($request->faqs, true) ?? [];
            $recipe->faqs()->delete();
            foreach ($faqs as $i => $faq) {
                RecipeFaq::create([
                    'recipe_id' => $recipe->id,
                    'question' => $faq['question'] ?? '',
                    'answer' => $faq['answer'] ?? '',
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}
