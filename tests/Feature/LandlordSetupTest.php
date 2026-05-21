<?php

use App\Models\Landlord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('landlord setup page displays step 1 by default', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup');

    $response->assertOk();
    $response->assertViewIs('landlord.setup');
    $response->assertViewHas('step', 1);
});

test('landlord setup returns 404 for invalid step numbers', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup/5');

    $response->assertStatus(404);
});

test('landlord setup prevents skipping steps', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup/3');

    $response->assertRedirect('/landlord/setup/2');
});

test('step 1 can be completed with valid data', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    $file = UploadedFile::fake()->image('profile.jpg');

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/1', [
            'name' => 'John Doe',
            'profile_picture' => $file,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/landlord/setup/2');

    // Check session data
    $this->assertArrayHasKey('name', session('landlord_setup_data'));
    $this->assertSame('John Doe', session('landlord_setup_data')['name']);
    $this->assertArrayHasKey('profile_picture_path', session('landlord_setup_data'));

    // Check file was stored
    Storage::disk('public')->assertExists(session('landlord_setup_data')['profile_picture_path']);
});

test('step 1 requires name and profile picture', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/1', []);

    $response->assertSessionHasErrors(['name', 'profile_picture']);
});

test('step 2 can be completed with valid password', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    // Set up step 1 completion
    session(['landlord_completed_steps' => [1]]);

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/2', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/landlord/setup/3');

    $this->assertArrayHasKey('password', session('landlord_setup_data'));
});

test('step 2 requires password confirmation', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    session(['landlord_completed_steps' => [1]]);

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/2', [
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

    $response->assertSessionHasErrors(['password']);
});

test('step 3 can be completed with QR codes', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    // Set up previous steps completion
    session([
        'landlord_completed_steps' => [1, 2],
        'landlord_setup_data' => [
            'name' => 'John Doe',
            'password' => 'password123',
        ],
    ]);

    $gcashFile = UploadedFile::fake()->image('gcash.jpg');
    $mayaFile = UploadedFile::fake()->image('maya.jpg');

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/3', [
            'gcash_qr' => $gcashFile,
            'maya_qr' => $mayaFile,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('toast.success');

    // Check user was updated
    $user->refresh();
    $this->assertSame('John Doe', $user->name);
    $this->assertTrue((bool) $user->profile_completed);
    $this->assertNotNull($user->profile_picture);

    // Check landlord QR codes were saved
    $landlord->refresh();
    $this->assertNotNull($landlord->gcash_qr);
    $this->assertNotNull($landlord->maya_qr);

    // Check session was cleaned up
    $this->assertNull(session('landlord_setup_data'));
    $this->assertNull(session('landlord_completed_steps'));
});

test('back navigation works correctly', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    // Test back from step 3 to step 2
    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup/back/3');

    $response->assertRedirect('/landlord/setup/2');

    // Test back from step 2 to step 1
    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup/back/2');

    $response->assertRedirect('/landlord/setup/1');

    // Test back from step 1 stays at step 1
    $response = $this
        ->actingAs($user)
        ->get('/landlord/setup/back/1');

    $response->assertRedirect('/landlord/setup/1');
});

test('existing images can be replaced in step 1', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    // Set up existing profile picture
    $oldFile = UploadedFile::fake()->image('old-profile.jpg');
    $oldPath = $oldFile->store('temp/profile_pictures', 'public');

    session(['landlord_setup_data' => ['profile_picture_path' => $oldPath]]);
    Storage::disk('public')->put($oldPath, 'old content');

    $newFile = UploadedFile::fake()->image('new-profile.jpg');

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/1', [
            'name' => 'John Doe',
            'profile_picture' => $newFile,
        ]);

    $response->assertSessionHasNoErrors();

    // Old file should be deleted
    Storage::disk('public')->assertMissing($oldPath);

    // New file should exist
    $newPath = session('landlord_setup_data')['profile_picture_path'];
    Storage::disk('public')->assertExists($newPath);
    $this->assertNotSame($oldPath, $newPath);
});

test('files are moved to permanent location on completion', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;

    // Create temp files
    $profileFile = UploadedFile::fake()->image('profile.jpg');
    $gcashFile = UploadedFile::fake()->image('gcash.jpg');
    $mayaFile = UploadedFile::fake()->image('maya.jpg');

    $profilePath = $profileFile->store('temp/profile_pictures', 'public');
    $gcashPath = $gcashFile->store('temp/qr_codes', 'public');
    $mayaPath = $mayaFile->store('temp/qr_codes', 'public');

    session([
        'landlord_completed_steps' => [1, 2],
        'landlord_setup_data' => [
            'name' => 'John Doe',
            'password' => 'password123',
            'profile_picture_path' => $profilePath,
            'gcash_qr_path' => $gcashPath,
            'maya_qr_path' => $mayaPath,
        ],
    ]);

    $response = $this
        ->actingAs($user)
        ->post('/landlord/setup/3', []);

    $response->assertSessionHasNoErrors();

    // Temp files should be gone
    Storage::disk('public')->assertMissing($profilePath);
    Storage::disk('public')->assertMissing($gcashPath);
    Storage::disk('public')->assertMissing($mayaPath);

    // Files should be in permanent locations
    $user->refresh();
    $landlord->refresh();

    Storage::disk('public')->assertExists($user->profile_picture);
    Storage::disk('public')->assertExists($landlord->gcash_qr);
    Storage::disk('public')->assertExists($landlord->maya_qr);

    // Check paths are correct
    $this->assertStringStartsWith('profile_pictures/', $user->profile_picture);
    $this->assertStringStartsWith('qr_codes/', $landlord->gcash_qr);
    $this->assertStringStartsWith('qr_codes/', $landlord->maya_qr);
});
