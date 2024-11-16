<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

function asOrganizer(): TestCase
{
    $user = User::factory()->create([
        'role' => UserRole::ORGANIZER,
        'password' => Hash::make('password123')
    ]);

    return test()->actingAs($user);
}

function asAttendee(): TestCase
{
    $user = User::factory()->create([
        'role' => UserRole::ATTENDEE,
        'password' => Hash::make('password123')
    ]);

    return test()->actingAs($user);
}
