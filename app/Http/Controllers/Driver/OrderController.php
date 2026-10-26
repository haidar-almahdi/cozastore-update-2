<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $driverId = Auth::id();

        $availableOrders = Order::with(['user', 'items.product'])
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->latest()
            ->paginate(10, ['*'], 'available_page');

        $myActiveOrders = Order::with(['user', 'items.product'])
            ->where('driver_id', $driverId)
            ->whereIn('status', ['processing'])
            ->latest()
            ->paginate(10, ['*'], 'active_page');

        $myDeliveredOrders = Order::with(['user'])
            ->where('driver_id', $driverId)
            ->where('status', 'completed')
            ->latest()
            ->paginate(10, ['*'], 'delivered_page');

        return view('driver.orders.index', compact('availableOrders', 'myActiveOrders', 'myDeliveredOrders'));
    }

    public function show(Order $order)
    {
        if ($order->driver_id !== Auth::id() && !is_null($order->driver_id)) {
            abort(403);
        }

        $order->load(['user', 'items.product']);
        return view('driver.orders.show', compact('order'));
    }

    public function accept(Order $order)
    {
        if ($order->status !== 'pending' || !is_null($order->driver_id)) {
            return back()->with('error', 'This order is not available for pickup.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'driver_id' => Auth::id(),
                'status' => 'processing',
            ]);
        });

        return back()->with('success', 'Order accepted successfully.');
    }

    public function markDelivered(Order $order)
    {
        if ($order->driver_id !== Auth::id()) {
            return back()->with('error', 'You can only deliver your assigned orders.');
        }

        if ($order->status === 'completed') {
            return back()->with('info', 'Order is already marked as delivered.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'completed']);
        });

        if ($order->user) {
            /** @var LoyaltyService $loyalty */
            $loyalty = app(LoyaltyService::class);
            $loyalty->awardDeliveredOrderPoints($order->user, $order);
        }

        return redirect()->route('driver.orders.index')->with('success', 'Order marked as delivered.');
    }
}
