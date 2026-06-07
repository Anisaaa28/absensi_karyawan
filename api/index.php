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

// Regenerate bootstrap cache files on Vercel to ensure providers are registered correctly
// Delete any stale cache to force fresh generation
@unlink('/tmp/bootstrap/cache/services.php');
@unlink('/tmp/bootstrap/cache/packages.php');

// Copy fresh bootstrap cache files from read-only filesystem
$bootstrapCacheFiles = [
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/services.php',
];

foreach ($bootstrapCacheFiles as $file) {
    if (file_exists($file)) {
        $tmpFile = str_replace(__DIR__ . '/../bootstrap', '/tmp/bootstrap', $file);
        @copy($file, $tmpFile);
    }
}

// Tell Laravel where to write compiled views (Blade cache).
if (! getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
}
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Tell Laravel to use /tmp for bootstrap cache
if (! getenv('BOOTSTRAP_CACHE_PATH')) {
    putenv('BOOTSTRAP_CACHE_PATH=/tmp/bootstrap/cache');
}
$_ENV['BOOTSTRAP_CACHE_PATH'] = '/tmp/bootstrap/cache';
$_SERVER['BOOTSTRAP_CACHE_PATH'] = '/tmp/bootstrap/cache';

// Tell Laravel to use /tmp for config cache
if (! getenv('CONFIG_CACHE_PATH')) {
    putenv('CONFIG_CACHE_PATH=/tmp/bootstrap/cache/config.php');
}
$_ENV['CONFIG_CACHE_PATH'] = '/tmp/bootstrap/cache/config.php';
$_SERVER['CONFIG_CACHE_PATH'] = '/tmp/bootstrap/cache/config.php';

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
