<?php

use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\User;

test('profile page is displayed for tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = $tenant->user;

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
    $response->assertViewIs('profile.edit'); // Adjust view name as needed
});

test('profile page is displayed for landlord', function () {
    $user = User::factory()->landlord()->create([
        'profile_completed' => true,
    ]);

    $landlord = Landlord::factory()->create([
        'user_id' => $user->id,
    ]);
    $user = $landlord->user;

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
    $response->assertViewIs('profile.edit'); // Adjust view name as needed
});

test('profile page redirects incomplete landlord to setup', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;
    $user->profile_completed = false;
    $user->save();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertRedirect('/landlord/setup');
});

test('profile information can be updated for regular user', function () {
    $user = User::factory()->create([
        'profile_completed' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@gmail.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('profile information can be updated for tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = $tenant->user;

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Tenant',
            'email' => 'tenant@updated.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertSame('Updated Tenant', $user->name);
    $this->assertSame('tenant@updated.com', $user->email);
});

test('profile information can be updated for completed landlord', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;
    $user->profile_completed = true;
    $user->save();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Updated Landlord',
            'email' => 'landlord@updated.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();
    $this->assertSame('Updated Landlord', $user->name);
    $this->assertSame('landlord@updated.com', $user->email);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'profile_completed' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create([
        'profile_completed' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('tenant can delete their account', function () {
    $tenant = Tenant::factory()->create();
    $user = $tenant->user;

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
    $this->assertNull($tenant->fresh());
});

test('landlord can delete their account', function () {
    $landlord = Landlord::factory()->create();
    $user = $landlord->user;
    $user->profile_completed = true;
    $user->save();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
    $this->assertNull($landlord->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create([
        'profile_completed' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
