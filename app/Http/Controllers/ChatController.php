<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //create new message
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        $senderId = $request->user()->id;
        $order = Order::with(['driver', 'customer'])->findOrFail($data['order_id']);

        if ($senderId == $order->customer->id) {
            $receiverId = $order->driver->id;
        } elseif ($senderId == $order->driver->id) {
            $receiverId = $order->customer->id;
        } else {
            return $this->sendError('You are not part of this order', 403);
        }

        $conversation = Conversation::where('order_id', $data['order_id'])
            ->where(function($q) use ($senderId, $receiverId) {
                $q->where(function($q2) use ($senderId, $receiverId) {
                    $q2->where('user1_id', $senderId)->where('user2_id', $receiverId);
                })->orWhere(function($q2) use ($senderId, $receiverId) {
                    $q2->where('user1_id', $receiverId)->where('user2_id', $senderId);
                });
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'order_id' => $data['order_id'],
                'user1_id' => $senderId,
                'user2_id' => $receiverId,
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'content' => $data['content'],
        ]);

        return $this->sendResponse([
            'conversation_id' => $conversation->id,
            'message' => $message
        ], '');
    }

    //fetching new messages for a conversation or chat history
    public function index(Request $request)
    {
        $conversationId = $request->conversation_id;
        $afterId = $request->after_id;
        $perPage = 20;

        $query = Message::where('conversation_id', $conversationId);

        if ($afterId) {
            $messages = $query->where('id', '>', $afterId)
                ->orderBy('id', 'asc')
                ->get();

            return $this->sendResponse([
                'mode' => 'polling',
                'data' => $messages
            ], '');
        }

        $messages = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        $messages->getCollection()->transform(function ($item) {
            return $item;
        })->reverse();

        return $this->sendResponse([
            'mode' => 'pagination',
            'data' => $messages
        ], '');
    }

    public function conversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['lastMessage', 'user1', 'user2'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        // Transform data to include partner info and last message
        $data = $conversations->map(function ($conv) use ($userId) {
            $partner = $conv->user1_id == $userId ? $conv->user2 : $conv->user1;

            return [
                'id' => $conv->id,
                'order_id' => $conv->order_id,
                'partner' => [
                    'id' => $partner->id,
                    'name' => $partner->name,
                    'small_photo' => $partner->photo->small_path ?? null,
                ],
                'last_message' => $conv->lastMessage
                    ? [
                        'content' => $conv->lastMessage->content,
                        'created_at' => $conv->lastMessage->created_at,
                    ]
                    : null,
            ];
        });

        return $this->sendResponse($data, '');
    }
}
