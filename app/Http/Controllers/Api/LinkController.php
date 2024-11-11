<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Link::query()->valid();

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
                    ->orWhere('telegram_username', 'like', "%{$search}%");
            });
        }

        // 排序
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        return LinkResource::collection(
            $query->paginate($request->input('per_page', 15))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Link $Link): LinkResource
    {
        return new LinkResource($Link);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'introduction' => 'required|string',
            'url' => 'required|url|unique:telegram_links',
            'type' => 'required|in:bot,channel,group,person,message',
            'telegram_username' => 'required|string|max:255',
        ]);

        $validated['is_by_user'] = true;
        $validated['user_id'] = auth()->id;
        $validated['is_valid'] = false; // 需要管理员审核

        $Link = Link::create($validated);

        return new LinkResource($Link);
    }
}