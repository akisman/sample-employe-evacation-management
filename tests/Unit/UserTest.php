<?php

use App\Core\DB;
use App\Models\User;


beforeEach(function () {
    // Mock PDOStatement
    $this->stmt = \Mockery::mock(PDOStatement::class);

    // Mock PDO
    $this->pdo = \Mockery::mock(PDO::class);

    // Mock DB::connect() static alias to return mocked PDO
    \Mockery::mock('alias:' . DB::class)
        ->shouldReceive('connect')
        ->andReturn($this->pdo);
});

afterEach(function () {
    \Mockery::close();
});

it('User::find returns user object when found', function () {
    $userData = [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'password' => 'secret',
        'role' => 'employee',
        'employee_code' => 123,
    ];

    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE id = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with([1])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn($userData);

    $user = User::find(1);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Alice');
});

it('User::find returns null if user not found', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE id = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with([999])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturnFalse();

    $user = User::find(999);

    expect($user)->toBeNull();
});

it('User::findByEmail returns user object when found', function () {
    $userData = [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'password' => 'secret',
        'role' => 'employee',
        'employee_code' => 123,
    ];

    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE email = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with(['alice@example.com'])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn($userData);

    $user = User::findByEmail('alice@example.com');

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Alice');
});

it('User::findByEmail returns null if user not found', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE email = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with(['example@example.com'])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturnFalse();

    $user = User::findByEmail('example@example.com');

    expect($user)->toBeNull();
});

it('User::findByEmployeeCode returns user object when found', function () {
    $userData = [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'password' => 'secret',
        'role' => 'employee',
        'employee_code' => 1234567,
    ];

    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE employee_code = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with([1234567])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturn($userData);

    $user = User::findByEmployeeCode(1234567);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Alice');
});

it('User::findByEmployeeCode returns null if user not found', function () {
    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE employee_code = ?')
        ->andReturn($this->stmt);

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with([9999999])
        ->andReturnTrue();

    $this->stmt->shouldReceive('fetch')
        ->once()
        ->with(PDO::FETCH_ASSOC)
        ->andReturnFalse();

    $user = User::findByEmployeeCode(9999999);

    expect($user)->toBeNull();
});

it('User::verifyPassword returns true for correct password', function () {
    $userData = [
        'id' => 1,
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'password' => password_hash('mypassword', PASSWORD_DEFAULT),
        'role' => 'employee',
        'employee_code' => 123,
    ];

    $user = new User($userData);

    expect($user->verifyPassword('mypassword'))->toBeTrue();
    expect($user->verifyPassword('wrongpassword'))->toBeFalse();
});

it('User::create inserts and returns created user', function () {
    $userData = [
        'name' => 'Charlie',
        'email' => 'charlie@example.com',
        'password' => 'pass123',
        'role' => 'employee',
        'employee_code' => 789,
    ];

    $this->stmt->shouldReceive('execute')
        ->once()
        ->with([
            $userData['name'],
            $userData['email'],
            $userData['password'],
            $userData['role'],
            $userData['employee_code']
        ])
        ->andReturnTrue();

    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with("INSERT INTO users (name, email, password, role, employee_code) VALUES (?, ?, ?, ?, ?)")
        ->andReturn($this->stmt);

    // Simulate lastInsertId
    $this->pdo->shouldReceive('lastInsertId')->once()->andReturn(3);

    // Prepare for the find() call after insert
    $findStmt = \Mockery::mock(PDOStatement::class);

    $findUserData = $userData + ['id' => 3];
    $findStmt->shouldReceive('execute')->once()->with([3])->andReturnTrue();
    $findStmt->shouldReceive('fetch')->once()->with(PDO::FETCH_ASSOC)->andReturn($findUserData);

    $this->pdo->shouldReceive('prepare')
        ->once()
        ->with('SELECT * FROM users WHERE id = ?')
        ->andReturn($findStmt);

    $user = User::create($userData);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->id)->toBe(3);
    expect($user->name)->toBe('Charlie');
});
