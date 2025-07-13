<?php

use App\Controllers\VacationController;
use App\Models\User;
use App\Models\Vacation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestVacationController extends VacationController
{
    public $mockUser = null;

    public function getAuthenticatedUser(): ?User
    {
        return $this->mockUser;
    }
}

uses()->group('VacationController');

beforeEach(function () {
    $this->controller = \Mockery::mock(TestVacationController::class)->makePartial();
});

it('index returns 401 if not authenticated', function () {
    $this->controller->mockUser = null; // simulate no auth
    $response = $this->controller->index();

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getStatusCode())->toBe(401);
    expect(json_decode($response->getContent(), true)['message'])->toBe('Unauthorized');
});

it('index returns vacations for authenticated user', function () {
    $vacations = ['vac1', 'vac2'];

    $user = \Mockery::mock(User::class);
    $user->shouldReceive('vacations')->andReturn($vacations);

    $this->controller->mockUser = $user;

    $response = $this->controller->index();

    expect($response->getStatusCode())->toBe(200);
    expect(json_decode($response->getContent(), true))->toBe($vacations);
});

it('all returns 401 if not authenticated', function () {
    $response = $this->controller->all();

    expect($response->getStatusCode())->toBe(401);
    expect(json_decode($response->getContent(), true)['message'])->toBe('Unauthorized');
});

it('all returns 403 if user is not manager', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isManager')->andReturn(false);

    $this->controller->mockUser = $user;
    $response = $this->controller->all();

    expect($response->getStatusCode())->toBe(403);
    expect(json_decode($response->getContent(), true)['message'])->toContain('Forbidden');
});

it('all returns all vacations for manager', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isManager')->andReturn(true);

    $vacations = ['vac1', 'vac2'];
    // Mock Vacation::all() static call
    \Mockery::mock('alias:' . Vacation::class)
        ->shouldReceive('all')
        ->andReturn($vacations);

    $this->controller->mockUser = $user;

    $response = $this->controller->all();

    expect($response->getStatusCode())->toBe(200);
    expect(json_decode($response->getContent(), true))->toBe($vacations);
});

it('store returns 401 if not authenticated', function () {
    $req = new Request([], [], [], [], [], [], json_encode([
        'start_date' => '2025-07-01',
        'end_date' => '2025-07-10',
        'reason' => 'Vacation'
    ]));

    $response = $this->controller->store($req);

    expect($response->getStatusCode())->toBe(401);
    expect(json_decode($response->getContent(), true)['message'])->toBe('Unauthorized');
});

it('store returns 422 if start_date or end_date missing', function () {
    $user = \Mockery::mock(User::class);
//    $this->controller->shouldReceive('getAuthenticatedUser')->andReturn($user);
    $this->controller->mockUser = $user;

    // Fake session user id for store() method
    $_SESSION['user_id'] = 1;

    $req = new Request([], [], [], [], [], [], json_encode([
        'start_date' => null,
        'end_date' => '2025-07-10',
    ]));

    $response = $this->controller->store($req);
    expect($response->getStatusCode())->toBe(422);
    expect(json_decode($response->getContent(), true)['message'])->toContain('required');

    $req = new Request([], [], [], [], [], [], json_encode([
        'start_date' => '2025-07-01',
        'end_date' => null,
    ]));

    $response = $this->controller->store($req);
    expect($response->getStatusCode())->toBe(422);
    expect(json_decode($response->getContent(), true)['message'])->toContain('required');
});
