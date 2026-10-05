<?php
/* ============================================================
   VERCEL FRONT CONTROLLER
   Vercel runs PHP only from /api, so every request is routed
   here (see vercel.json) and dispatched to the matching page
   in the project root:  /about  or  /about.php  ->  about.php
   ============================================================ */

$root = dirname(__DIR__);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$page = preg_replace('/\.php$/', '', trim($path, '/'));
if ($page === '') $page = 'index';

/* Dev-only utilities that should not be public */
$blocked = ['check-files', 'debug-cutouts', 'generate-logos', 'warm-press'];

if (!preg_match('/^[a-z0-9-]+$/', $page)
    || in_array($page, $blocked, true)
    || !is_file($root . '/' . $page . '.php')) {
    http_response_code(404);
    $page = 'index';
}

/* Make the page believe it was requested directly */
$_SERVER['SCRIPT_NAME']     = '/' . $page . '.php';
$_SERVER['PHP_SELF']        = '/' . $page . '.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/' . $page . '.php';
$_SERVER['DOCUMENT_ROOT']   = $root;

chdir($root);
require $root . '/' . $page . '.php';
