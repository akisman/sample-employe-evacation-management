<?php

use App\Controllers\VacationController;
use App\Models\User;
use App\Models\Vacation;
use Symfony\Component\HttpFoundation\JsonResponse;

uses()->group('HandlesVacations');

beforeEach(function () {
    // Make a test double for controller so we can override getAuthenticatedUser()
    $this->controller = new class {
        use App\Traits\HandlesVacations;

        public $mockUser = null;

        public function getAuthenticatedUser(): ?User
        {
            return $this->mockUser;
        }
    };
});

afterEach(function () {
    \Mockery::close();
});

it('returns 400 if id is missing or invalid', function () {
    $response = $this->controller->withVacation([], fn () => null);
    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatusCode())->toBe(400);
    expect(json_decode($response->getContent(), true)['message'])->toBe('Invalid or missing id');

    $response = $this->controller->withVacation(['id' => 'abc'], fn () => null);
    expect($response->getStatusCode())->toBe(400);
});

it('returns 401 if user is not authenticated', function () {
    $this->controller->mockUser = null;

    $response = $this->controller->withVacation(['id' => '1'], fn () => null);
    expect($response->getStatusCode())->toBe(401);
});

it('returns 403 if user is not manager', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isManager')->andReturn(false);
    $this->controller->mockUser = $user;

    $response = $this->controller->withVacation(['id' => '1'], fn () => null);
    expect($response->getStatusCode())->toBe(403);
});

it('returns 404 if vacation not found', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isManager')->andReturn(true);
    $this->controller->mockUser = $user;

    // Mock Vacation::find to return null for id=1
    // Use patch for static method
    \Mockery::mock('alias:' . Vacation::class)
        ->shouldReceive('find')
        ->with(1)
        ->andReturn(null);

    $response = $this->controller->withVacation(['id' => '1'], fn () => null);
    expect($response->getStatusCode())->toBe(404);
});
