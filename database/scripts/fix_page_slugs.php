<?php
/**
 * Renombra los slugs cortos a slugs descriptivos para mejor SEO.
 * privacidad → politica-de-privacidad
 * cookies    → politica-de-cookies
 */
$renames = [
    'privacidad' => 'politica-de-privacidad',
    'cookies'    => 'politica-de-cookies',
];

foreach ($renames as $oldSlug => $newSlug) {
    $page = \App\Models\Page::where('slug', $oldSlug)->first();
    if ($page) {
        $page->slug = $newSlug;
        $page->save();
        echo "  Renamed: {$oldSlug} → {$newSlug}" . PHP_EOL;
    } else {
        echo "  Not found: {$oldSlug}" . PHP_EOL;
    }
}

echo PHP_EOL . "Done." . PHP_EOL;
