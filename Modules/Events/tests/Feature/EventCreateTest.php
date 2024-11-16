<?php

use Illuminate\Http\Response;
use Modules\Events\Models\Event;

uses(Tests\TestCase::class);

it('creates an event successfully for an authenticated user (ORGANIZER) with correct data', function () {
    asOrganizer();

    $eventData = [
        'title' => 'Test Event',
        'event_date' => now()->addDays(5)->toDateString(),
        'location' => 'Test Location',
        'description' => 'test event',
        'ticket_types' => [
            ['name' => 'VIP', 'price' => 100, 'quantity' => 50],
            ['name' => 'Regular', 'price' => 50, 'quantity' => 100],
        ]
    ];

    $response = $this->postJson(route('events.store'), $eventData);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJson([
        'success' => true,
        'message' => 'Event created successfully!',
    ]);

    $this->assertDatabaseHas('events', [
        'title' => 'Test Event',
        'location' => 'Test Location',
        'event_date' => $eventData['event_date'],
    ]);

    $event = Event::where('title', 'Test Event')->first();
    $this->assertCount(2, $event->ticketTypes); // Check if 2 ticket types are created

    $this->assertDatabaseHas('ticket_types', [
        'event_id' => $event->id,
        'name' => 'VIP',
        'price' => 100,
        'quantity' => 50,
    ]);

    $this->assertDatabaseHas('ticket_types', [
        'event_id' => $event->id,
        'name' => 'Regular',
        'price' => 50,
        'quantity' => 100,
    ]);
});

it('returns a validation error if required fields are missing', function () {
    asOrganizer();

    // Missing some field
    $eventData = [
        'location' => 'Test Location',
        'ticket_types' => [
            ['name' => 'VIP', 'price' => 100, 'quantity' => 50],
            ['name' => 'Regular', 'price' => 50, 'quantity' => 100],
        ]
    ];

    $response = $this->postJson(route('events.store'), $eventData);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJsonValidationErrors(['title', 'event_date', 'description']);
});

it('returns an unauthorized error if the user is not authorized to create events', function () {
    asAttendee();

    $eventData = [
        'title' => 'Test Event',
        'event_date' => now()->addDays(5)->toDateString(),
        'location' => 'Test Location',
        'description' => 'test event',
        'ticket_types' => [
            ['name' => 'VIP', 'price' => 100, 'quantity' => 50],
            ['name' => 'Regular', 'price' => 50, 'quantity' => 100],
        ]
    ];

    $response = $this->postJson(route('events.store'), $eventData);
    $response->assertStatus(Response::HTTP_FOUND); // redirect 302 because attendee does not have access

});
