<?php
/**
 * Vercel entry point for Laravel application.
 *
 * Routes every incoming request through Laravel's public/index.php
 * so the framework's routing, middleware, and session handling work
 * the same as in a traditional LAMP/LEMP setup.
 *
 * Vercel's serverless filesystem is read-only except /tmp, so we
 * pre-create the writable directories Laravel needs (compiled views,
 * sessions, cache, logs) under /tmp and override the relevant paths
 * before booting the framework.
 */

declare(strict_types=1);

// Writable paths on Vercel — filesystem is read-only except /tmp.
$writablePaths = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($writablePaths as $path) {
    if (! is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

// FIX: Do NOT set APP_CONFIG_CACHE or APP_ROUTES_CACHE to empty strings.
// Laravel's normalizeCachePath() treats empty string as a relative path
// and resolves it to the base directory, causing "require(/var/task/user)" to fail.
// Instead, point them to a non-existent file in /tmp so Laravel skips the cache
// and loads config files dynamically.
$cacheOverrides = [
    'APP_CONFIG_CACHE'  => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE'  => '/tmp/bootstrap/cache/routes-v7.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_EVENTS_CACHE'  => '/tmp/bootstrap/cache/events.php',
];

foreach ($cacheOverrides as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Set environment variables for Vercel's /tmp filesystem
// These must be set BEFORE Laravel's bootstrap process
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('BOOTSTRAP_CACHE_PATH=/tmp/bootstrap/cache');

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['BOOTSTRAP_CACHE_PATH'] = '/tmp/bootstrap/cache';
$_SERVER['BOOTSTRAP_CACHE_PATH'] = '/tmp/bootstrap/cache';

error_log('[vercel-entry] booting Laravel, APP_DEBUG=' . getenv('APP_DEBUG') . ' APP_KEY=' . (getenv('APP_KEY') ? 'set' : 'EMPTY') . ' DB_HOST=' . getenv('DB_HOST'));

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    error_log('[vercel-entry] FATAL: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
    error_log('[vercel-entry] TRACE: ' . $e->getTraceAsString());

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== VERCEL DEPLOYMENT ERROR ===\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    echo "Hint: Check Vercel Dashboard → Settings → Environment Variables.\n";
    echo "Most common cause: missing APP_KEY, DB_HOST, or DB_PASSWORD.\n";
    exit(1);
}
