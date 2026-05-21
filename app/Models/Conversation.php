<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'participants',
        'title',
        'metadata',
        'last_message_at',
    ];

    protected $casts = [
        'participants' => 'array',
        'metadata' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    // Check if user is participant in conversation
    public function hasParticipant($userId): bool
    {
        $participants = array_map('intval', $this->participants);

        return in_array((int) $userId, $participants);
    }

    // Get conversations for a specific user
    public static function forUser($userId)
    {
        // Alternative approach using raw SQL for better compatibility
        return static::whereRaw('JSON_CONTAINS(participants, ?)', [json_encode((int) $userId)])
            ->orWhereRaw('JSON_CONTAINS(participants, ?)', [json_encode((string) $userId)]);
    }

    // Get other participant (for 1-on-1 conversations)
    public function getOtherParticipant($currentUserId)
    {
        $otherParticipantId = collect($this->participants)->first(fn ($id) => $id != $currentUserId);

        return User::find($otherParticipantId);
    }
}
