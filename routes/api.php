<?php

use FastRoute\RouteCollector;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\VacationController;

return FastRoute\simpleDispatcher(function (RouteCollector $r) {

    // Vacation Routes
    $r->addRoute('GET', '/api/vacations', VacationController::class.'@index');
    $r->addRoute('GET', '/api/vacations/pending', VacationController::class.'@all');
    $r->addRoute('POST', '/api/vacations', VacationController::class.'@store');
    $r->addRoute('PUT', '/api/vacations/{id:\d+}/approve', VacationController::class.'@approve');
    $r->addRoute('PUT', '/api/vacations/{id:\d+}/decline', VacationController::class.'@decline');

    // Auth Routes
    $r->addRoute('GET', '/api/me', AuthController::class.'@me');
    $r->addRoute('POST', '/api/login', AuthController::class.'@login');
    $r->addRoute('POST', '/api/logout', AuthController::class.'@logout');

    // User Routes
    $r->addRoute('GET', '/api/users', UserController::class.'@index');
    $r->addRoute('GET', '/api/users/{id:\d+}', UserController::class.'@show');
    $r->addRoute('POST', '/api/users', UserController::class.'@store');
    $r->addRoute('PUT', '/api/users/{id:\d+}', UserController::class.'@update');
    $r->addRoute('DELETE', '/api/users/{id:\d+}', UserController::class.'@destroy');

});
