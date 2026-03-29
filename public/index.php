<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/**
 * Allow the same public/index.php to work both when served from the repo's
 * native /public directory and when copied to a hosting folder such as
 * /public_html that lives alongside the project root.
 */
function resolveLaravelBasePath(): string
{
    $candidates = array_filter([
        getenv('APP_BASE_PATH') ?: null,
        $_SERVER['APP_BASE_PATH'] ?? null,
        dirname(__DIR__),
        __DIR__.'/../marvinbaptista',
    ]);

    foreach ($candidates as $candidate) {
        $basePath = rtrim($candidate, DIRECTORY_SEPARATOR);

        if (
            is_file($basePath.'/vendor/autoload.php')
            && is_file($basePath.'/bootstrap/app.php')
        ) {
            return $basePath;
        }
    }

    throw new RuntimeException('Unable to resolve the Laravel base path.');
}

$basePath = resolveLaravelBasePath();

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
