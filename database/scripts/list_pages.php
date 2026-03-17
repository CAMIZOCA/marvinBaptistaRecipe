<?php
$pages = \App\Models\Page::select('id', 'slug', 'title')->orderBy('id')->get();
foreach ($pages as $p) {
    echo $p->id . ' | ' . $p->slug . ' | ' . $p->title . PHP_EOL;
}
