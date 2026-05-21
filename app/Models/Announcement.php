<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_id',
        'title',
        'description',
        'type',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeRelevantToLandlord(Builder $query, Landlord $landlord)
    {
        // Landlords can see all announcements
        // we might want to filter by landlord's properties in the future
        return $query;
    }

    public function scopeRelevantToTenant(Builder $query, Tenant $tenant)
    {
        $room = $tenant->room;
        $propertyId = $room ? $room->property_id : null;
        $roomId = $room ? $room->id : null;

        return $query->where(function ($q) use ($propertyId, $roomId) {
            // System announcements (no property and no room)
            $q->where(function ($q2) {
                $q2->whereNull('property_id')->whereNull('room_id');
            })
                // Property-wide announcements (for tenant's property, room_id null)
                ->orWhere(function ($q2) use ($propertyId) {
                    $q2->where('property_id', $propertyId)->whereNull('room_id');
                })
                // Room-specific announcements (for tenant's room)
                ->orWhere(function ($q2) use ($propertyId, $roomId) {
                    $q2->where('property_id', $propertyId)->where('room_id', $roomId);
                });
        });
    }

    public function scopeFilterByType(Builder $query, string $type, ?Tenant $tenant = null)
    {
        if ($type === 'all') {
            return $query;
        }

        if ($type === 'system') {
            return $query->whereNull('property_id')->whereNull('room_id');
        }

        if ($type === 'property') {
            if ($tenant) {
                // For tenants, filter by their property
                $room = $tenant->room;
                $propertyId = $room ? $room->property_id : null;

                return $query->where('property_id', $propertyId)->whereNull('room_id');
            } else {
                // For landlords, show all property announcements
                return $query->whereNotNull('property_id')->whereNull('room_id');
            }
        }

        if ($type === 'room') {
            if ($tenant) {
                // For tenants, filter by their specific room
                $room = $tenant->room;
                $propertyId = $room ? $room->property_id : null;
                $roomId = $room ? $room->id : null;

                return $query->where('property_id', $propertyId)->where('room_id', $roomId);
            } else {
                // For landlords, show all room announcements
                return $query->whereNotNull('property_id')->whereNotNull('room_id');
            }
        }

        return $query;
    }
}
