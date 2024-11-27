<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Chat::query()->valid();

        // 根据类型筛选
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // 搜索功能
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('introduction', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // 排序
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        return ChatResource::collection(
            $query->paginate($request->input('per_page', 15))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat): ChatResource
    {
        return new ChatResource($chat);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'introduction' => 'required|string',
            // 'url' => 'required|url|unique:telegram_chats',
            'type' => 'required|in:bot,channel,group,person,message',
            'username' => 'required|string|max:255',
        ]);

        $validated['source'] = 'manual';
        $validated['user_id'] = auth()->id;
        $validated['is_valid'] = false; // 需要管理员审核

        $chat = Chat::create($validated);

        return new ChatResource($chat);
    }
}