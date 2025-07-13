<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

[$container, $dispatcher] = require __DIR__ . '/../bootstrap/app.php';

$request = Request::createFromGlobals();
$path = $request->getPathInfo();

// Check if request is for a static asset (e.g. /assets/*.js, /assets/*.css)
if (preg_match('#^/assets/#', $path)) {
    // Let Caddy handle static assets
    (new Response('Not found', 404))->send();
    exit;
}

$routeInfo = $dispatcher->dispatch($request->getMethod(), $path);
$status    = $routeInfo[0];

switch ($status) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // No matching API route – return the SPA entry
        $html = file_get_contents(__DIR__.'/assets/index.html');
        (new Response($html, 200, ['Content-Type' => 'text/html']))->send();
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        (new Response('Method not allowed', 405))->send();
        break;

    case FastRoute\Dispatcher::FOUND:
        [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];
        // "Controller@method" style string
        [$class, $method] = explode('@', $handler);

        $response = (new $class)->{$method}($request, $vars);

        if (!$response instanceof Response) {
            $response = new Response($response);
        }
        $response->send();
        break;
}
