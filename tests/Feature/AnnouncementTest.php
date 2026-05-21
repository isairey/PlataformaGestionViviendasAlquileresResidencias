<?php

use App\Models\Announcement;
use App\Models\Tenant;

it('shows system-wide announcements to all tenants', function () {
    $tenant = Tenant::factory()->create();
    $announcement = Announcement::factory()->systemWide()->create([
        'title' => 'System Notice',
    ]);

    $this->actingAs($tenant->user)
        ->get('/announcements')
        ->assertSee($announcement->title);
});

it('shows property-wide announcements to tenants in that property', function () {
    $tenant = Tenant::factory()->create();
    $property = $tenant->room->property;

    $announcement = Announcement::factory()->propertyWide($property)->create([
        'title' => 'Property Notice',
    ]);

    $this->actingAs($tenant->user)
        ->get('/announcements')
        ->assertSee($announcement->title);
});

it('shows room-specific announcements to tenants in that room', function () {
    $tenant = Tenant::factory()->create();
    $room = $tenant->room;

    $announcement = Announcement::factory()->forRoom($room)->create([
        'title' => 'Room Notice',
    ]);

    $this->actingAs($tenant->user)
        ->get('/announcements')
        ->assertSee($announcement->title);
});

it('does not show announcements from unrelated rooms or properties', function () {
    $tenant = Tenant::factory()->create();

    $unrelated = Announcement::factory()->unrelatedTo()->create([
        'title' => 'Unrelated',
    ]);

    $this->actingAs($tenant->user)
        ->get('/announcements')
        ->assertDontSee($unrelated->title);
});
