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

// Copy entire bootstrap and config directories to /tmp for Vercel
// This ensures Laravel can find all configuration files
$directoriesToCopy = [
    __DIR__ . '/../bootstrap' => '/tmp/bootstrap',
    __DIR__ . '/../config' => '/tmp/config',
];

foreach ($directoriesToCopy as $source => $dest) {
    if (is_dir($source) && ! is_dir($dest)) {
        // Recursive directory copy
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            $target = $dest . substr($file->getPathname(), strlen($source));
            if ($file->isDir()) {
                @mkdir($target, 0755, true);
            } else {
                @copy($file->getPathname(), $target);
            }
        }
    }
}

// Disable config caching on Vercel - use dynamic config loading
putenv('APP_CONFIG_CACHE=');  // Empty = disable config caching
putenv('APP_ROUTES_CACHE=');  // Empty = disable route caching

// Set environment variables for Vercel's /tmp filesystem
// These must be set BEFORE Laravel's bootstrap process
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('BOOTSTRAP_CACHE_PATH=/tmp/bootstrap/cache');

// Set $_ENV and $_SERVER for Laravel's configuration system
$_ENV['APP_CONFIG_CACHE'] = '';  // Disable config caching
$_SERVER['APP_CONFIG_CACHE'] = '';
$_ENV['APP_ROUTES_CACHE'] = '';  // Disable route caching
$_SERVER['APP_ROUTES_CACHE'] = '';
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
