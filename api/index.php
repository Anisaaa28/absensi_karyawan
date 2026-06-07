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

// Writable paths on Vercel — filesystem is read-only outside /tmp.
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

// Tell Laravel where to write compiled views (Blade cache).
// Other paths (sessions, cache) are already redirected to /tmp
// via the env config block in vercel.json.
if (! getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
}
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

require __DIR__ . '/../public/index.php';
