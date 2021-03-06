<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the item.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $items = auth()->user()
            ->items()
            ->select('id', 'name', 'order', 'user_id')
            ->orderBy('order')
            ->get();

        return response()->json([
            'items' => $items
        ]);
    }

    /**
     * Store a newly created item in storage.
     *
     * @param  \App\Http\Requests\StoreItemRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        // If there is already a deleted item with the same name, restore it
        $existing_item = auth()->user()
            ->items()
            ->withTrashed()
            ->where('deleted_at', '!=', null)
            ->where('name', $request->input('name'))
            ->first();

        if ($existing_item) {
            return $this->restoreItem($existing_item);
        }

        $item = new Item();
        $item->user_id = auth()->id();
        $item->name = $request->input('name');
        $item->order = Item::nextItemOrder();
        $item->save();

        return response()->json([
            'item' => $item
        ]);
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \App\Http\Requests\UpdateItemRequest  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->name = $request->input('name');
        $item->save();

        return response()->json([
            'item' => $item
        ]);
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Item $item): JsonResponse
    {
        // shift order of every item after the deleted one down
        auth()->user()->shiftItemsOrder($item->order, auth()->user()->items()->max('order'), 'desc');

        $item->delete();

        return response()->json([]);
    }

    /**
     * Restore the specified item.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    private function restoreItem(Item $item): JsonResponse
    {
        // the item is restored and placed as the last one in the list
        $item->order = Item::nextItemOrder();
        $item->save();

        $item->restore();

        return response()->json([
            'item' => $item
        ]);
    }

    public function move(Item $item, int $order): JsonResponse
    {
        $order++;

        if ($order < 1) {
            $order = 1;
        }

        if ($item->order == $order) {
            return response()->json([
                'item' => $item
            ]);
        }
        $type = $order > $item->order ? 'desc' : 'asc';
        auth()->user()->shiftItemsOrder($item->order, $order, $order > $item->order ? 'desc' : 'asc');

        $item->order = $order;
        $item->save();

        return response()->json([
            'item' => $item,
            'order' => $order,
            'type' => $type
        ]);
    }
}
