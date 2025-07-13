<?php

use Dotenv\Dotenv;

require_once __DIR__.'/../vendor/autoload.php';

// 1. Load environment
(Dotenv::createImmutable(dirname(__DIR__)))->safeLoad();

// 2. Register a very small service container (anonymous class)
$container = new class {
    private array $bindings = [];
    function bind(string $id, callable $factory) { $this->bindings[$id] = $factory; }
    function make(string $id) { return ($this->bindings[$id])($this); }
};

// 3. Expose helpers
function app(string $id) { global $container; return $container->make($id); }

// 4. Pull in route definitions
$routes = require dirname(__DIR__).'/routes/api.php';

return [$container, $routes];
