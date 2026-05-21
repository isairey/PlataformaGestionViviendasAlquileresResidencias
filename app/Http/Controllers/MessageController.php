<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get all conversations and filter in PHP for better compatibility
        $conversations = Conversation::with(['latestMessage.user'])
            ->get()
            ->filter(function ($conversation) use ($userId) {
                return $conversation->hasParticipant($userId);
            })
            ->sortBy(function ($conversation) {
                // Sort by last_message_at if it exists, otherwise by created_at
                // Using negative timestamp to sort in descending order
                return $conversation->last_message_at ?
                    -$conversation->last_message_at->timestamp :
                    -$conversation->created_at->timestamp;
            });

        $availableUsers = User::where('id', '!=', auth()->id());

        // If current user is a tenant, only show landlords
        if (auth()->user()->role === 'tenant') {
            $availableUsers = $availableUsers->where('role', 'landlord');
        }

        $availableUsers = $availableUsers->orderBy('name')->get();

        return view('messages.index', compact('conversations', 'availableUsers'));
    }

    public function show(Conversation $conversation)
    {
        // Check if current user is participant
        if (! $conversation->hasParticipant(Auth::id())) {
            abort(403);
        }

        $messages = $conversation->messages()->with('user')->get();
        $otherParticipant = $conversation->getOtherParticipant(Auth::id());

        return view('messages.show', compact('conversation', 'messages', 'otherParticipant'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        try {
            $request->validate([
                'content' => 'nullable|string|max:1000', // Fixed: was max:10
                'attachment' => 'nullable|file|max:5120', // 5MB limit with allowed file types
            ], [
                'content.max' => 'Message content cannot exceed 1000 characters.',
                'attachment.max' => 'File size cannot exceed 5MB.',
                'attachment.mimes' => 'Only jpeg, png, jpg, gif, pdf, doc, docx, and txt files are allowed.',
            ]);

            if (! $conversation->hasParticipant(Auth::id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to send messages in this conversation.',
                ], 403);
            }

            $path = null;
            $type = 'text';
            $metadata = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('attachments', 'public');
                $type = str_starts_with($file->getMimeType(), 'image') ? 'image' : 'file';
                $metadata = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'url' => Storage::url($path),
                ];
            }

            if (! $request->filled('content') && ! $path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter a message or attach a file.',
                ], 422);
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'type' => $type,
                'metadata' => $metadata,
            ]);

            $conversation->update(['last_message_at' => now()]);
            broadcast(new MessageSent($message->load('user')));

            return response()->json([
                'message' => $message->load('user'),
                'success' => true,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Message store error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the message. Please try again.',
            ], 500);
        }
    }

    public function startConversation(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
        ]);

        $currentUserId = Auth::id();
        $recipientId = (int) $request->recipient_id;

        $currentUser = Auth::user();
        $recipient = User::findOrFail($recipientId);

        // Check if both users are tenants - if so, deny the conversation
        if ($currentUser->role === 'tenant' && $recipient->role === 'tenant') {
            return back()->withErrors([
                'recipient_id' => 'Tenant-to-tenant messaging is not allowed. You can only message landlords.',
            ])->withInput();
        }

        // Check if conversation already exists - filter in PHP for reliability
        $existingConversation = Conversation::all()
            ->filter(function ($conversation) use ($currentUserId, $recipientId) {
                $participants = array_map('intval', $conversation->participants);

                return in_array((int) $currentUserId, $participants) && in_array($recipientId, $participants);
            })
            ->first();

        if ($existingConversation) {
            return redirect()->route('messages.show', $existingConversation);
        }

        // Create new conversation
        $conversation = Conversation::create([
            'participants' => [(int) $currentUserId, $recipientId],
            'last_message_at' => now(),
        ]);

        // Create first message if provided
        if ($request->message) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $currentUserId,
                'content' => $request->message,
                'type' => 'text',
            ]);

            // Broadcast the message
            broadcast(new MessageSent($message->load('user')));
        }

        return redirect()->route('messages.show', $conversation);
    }
}
