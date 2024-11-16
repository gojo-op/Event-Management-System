<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\{assertDatabaseHas, post};

uses(Tests\TestCase::class);

it('redirects to dashboard for role 1 user (organizer)', function () {
    $adminUser = User::factory()->create([
        'role' => UserRole::ORGANIZER,
        'password' => Hash::make('password123')
    ]);

    $response = $this->postJson(route('login'), [
        'email' => $adminUser->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_OK);

    $responseData = $response->json();
    expect($responseData['success'])->toBe(true);
    expect($responseData['redirect'])->toBe(route('dashboard'));
});

it('redirects to home for role 0 user (attendee)', function () {
    $attendee = User::factory()->create([
        'role' => UserRole::ATTENDEE,
        'password' => bcrypt('password123')
    ]);

    $response = $this->postJson(route('login'), [
        'email' => $attendee->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(Response::HTTP_OK);

    $responseData = $response->json();
    expect($responseData['success'])->toBe(true);
    expect($responseData['redirect'])->toBe(route('home'));
});

it('fails login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson(route('login'), [
        'email' => 'testuser@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(Response::HTTP_OK);

    $responseData = $response->json();

    expect($responseData['success'])->toBe(false);
    expect($responseData['message'])->toContain('Invalid', 'credentials!');
});

it('fails login without credentials', function () {

    $response = $this->postJson(route('login'));

    expect($response)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $responseData = $response->json();
    expect($responseData['errors'])->toHaveKey('email');
    expect($responseData['errors']['email'][0])->toBe('The email field is required.');
    expect($responseData['errors'])->toHaveKey('password');
    expect($responseData['errors']['password'][0])->toBe('The password field is required.');
});


it('creates a new user during registration', function () {
    $data = [
        'name' => 'Vikas Kumar',
        'email' => 'vikaskumar.e1256@gmail.com',
        'password' => 'password123',
    ];

    $response = $this->postJson(route('register'), $data);

    $response->assertStatus(Response::HTTP_OK);

    $responseData = $response->json();
    expect($responseData['message'])->toBe('User registered successfully!');

    assertDatabaseHas('users', [
        'email' => $data['email'],
    ]);
});

it('returns an error if email already exists during registration', function () {
    User::factory()->create([
        'email' => 'vikaskumar.e1256@gmail.com',
    ]);

    $data = [
        'name' => 'Vikas Kumar',
        'email' => 'vikaskumar.e1256@gmail.com',
        'password' => 'password123',
    ];

    $response = $this->postJson(route('register'), $data);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $responseData = $response->json();
    expect($responseData['errors'])->toHaveKey('email');
    expect($responseData['errors']['email'][0])->toBe('The email has already been taken.');
});

it('logs out the user', function () {
    $logoutResponse = asOrganizer()->post('/logout');

    expect($logoutResponse->status())->toBe(302);

    $this->assertGuest();
});
