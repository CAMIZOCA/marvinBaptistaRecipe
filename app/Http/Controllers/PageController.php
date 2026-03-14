<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        $seo = [
            'title' => ($page->seo_title ?? $page->title) . ' - ' . config('app.name'),
            'description' => $page->seo_description ?? '',
            'canonical' => route('page.show', $page->slug),
        ];

        return view('pages.show', compact('page', 'seo'));
    }
}
