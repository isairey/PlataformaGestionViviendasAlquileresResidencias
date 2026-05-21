<?php

use App\Models\Room;
use Illuminate\Http\UploadedFile;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new tenants can register', function () {
    $room = Room::factory()->create();

    $step1Response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@gmail.com',
        'profile_picture' => UploadedFile::fake()->image('my-profile.jpg'),
    ]);

    $step1Response->assertRedirect('/register/2');

    // Step 2: Password
    $step2Response = $this->post('/register/2', [
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $step2Response->assertRedirect('/register/3');

    // Step 3: Room Code
    $step3Response = $this->post('/register/3', [
        'code' => $room->code,
    ]);

    $step3Response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
    Storage::disk('public')->assertExists(auth()->user()->profile_picture);
});

test('invalid registration fails', function () {
    $response = $this->get('/register/0');
    $response->assertNotFound();

    $response2 = $this->get('/register/abc');
    $response2->assertNotFound();
});
