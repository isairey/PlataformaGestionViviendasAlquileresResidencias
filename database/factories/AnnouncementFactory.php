<?php

namespace Database\Factories;

use App\Enums\AnnouncementType;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pick a random type
        $type = $this->faker->randomElement(AnnouncementType::cases())->value;

        // Set property_id and room_id based on type
        $property_id = null;
        $room_id = null;

        if ($type === AnnouncementType::PROPERTY->value) {
            $property_id = Property::factory();
        } elseif ($type === AnnouncementType::ROOM->value) {
            $property = Property::factory();
            $room = Room::factory()->for($property);
            $property_id = $property;
            $room_id = $room;
        }
        // For 'system', both stay null

        return [
            'type' => $type,
            'property_id' => $property_id,
            'room_id' => $room_id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function systemWide(): static
    {
        return $this->state(fn () => [
            'type' => AnnouncementType::SYSTEM->value,
            'property_id' => null,
            'room_id' => null,
        ]);
    }

    public function propertyWide(?Property $property = null): static
    {
        return $this->state(fn () => [
            'type' => AnnouncementType::PROPERTY->value,
            'property_id' => $property?->id ?? Property::factory(),
            'room_id' => null,
        ]);
    }

    public function forRoom(Room $room): static
    {
        return $this->state(fn () => [
            'type' => AnnouncementType::ROOM->value,
            'property_id' => $room->property_id,
            'room_id' => $room->id,
        ]);
    }

    public function unrelatedTo(?Property $excludeProperty = null): static
    {
        return $this->state(function () {
            // Create a new property that's definitely different
            $property = Property::factory()->create();
            $room = Room::factory()->for($property)->create();

            // Randomly choose between property-wide or room-specific
            $isRoomSpecific = $this->faker->boolean();

            return [
                'type' => $isRoomSpecific ? AnnouncementType::ROOM->value : AnnouncementType::PROPERTY->value,
                'property_id' => $property->id,
                'room_id' => $isRoomSpecific ? $room->id : null,
            ];
        });
    }
}
