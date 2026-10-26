<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\LoyaltyService;

class OrderController extends Controller
{


    /**
     * عرض قائمة جميع الفواتير للإدمن مع فلترة متقدمة
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])->latest();

        // فلترة حسب الحالة
        if ($request->filled('status') && in_array($request->status, ['pending', 'processing', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المبلغ الإجمالي (أكبر من أو يساوي)
        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }

        // فلترة حسب المبلغ الإجمالي (أقل من أو يساوي)
        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // فلترة حسب التاريخ (من)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // فلترة حسب التاريخ (إلى)
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // البحث في رقم الطلب أو اسم العميل أو الإيميل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // فلترة حسب العميل المحدد
        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }

        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['created_at', 'total_amount', 'status', 'order_number'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // جلب الفواتير مع التصفح
        $orders = $query->paginate(15)->withQueryString();

        // إحصائيات شاملة
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->sum('total_amount'),
            'monthly_revenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'avg_order_value' => Order::avg('total_amount'),
        ];

        // قائمة العملاء للفلترة
        $customers = User::where('role', 'customer')
            ->whereHas('orders')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.orders.index', compact('orders', 'stats', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->get();

        return view('admin.orders.create', compact('users', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $orderItems = [];

            // Validate and prepare order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                ];

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            // Create order
            $order = Order::create([
                'user_id' => $request->user_id,
                'order_number' => 'ORD-' . Str::random(5),
                'total_amount' => $total,
                'status' => 'pending',
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
                'status_history' => [[
                    'status' => 'pending',
                    'notes' => 'Order created by admin',
                    'changed_by' => Auth::id(),
                    'changed_at' => now()
                ]]
            ]);

            // Create order items
            $order->items()->createMany($orderItems);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        // Calculate order statistics
        $stats = [
            'total_items' => $order->items->sum('quantity'),
            'subtotal' => $order->items->sum(function($item) {
                return $item->price * $item->quantity;
            }),
            'status_history' => $order->status_history ?? [],
            'customer_orders' => Order::where('user_id', $order->user_id)
                ->where('id', '!=', $order->id)
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('admin.orders.show', compact('order', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return back()->with('error', 'Cannot edit completed or cancelled orders.');
        }

        $order->load(['user', 'items.product']);
        $users = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        if ($request->has('status')) {
            return $this->updateStatus($request, $order);
        }

        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return back()->with('error', 'Cannot edit completed or cancelled orders.');
        }

        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Restore original stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $total = 0;
            $orderItems = [];

            // Process new items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                ];

                // Update stock
                $product->decrement('stock', $item['quantity']);
            }

            // Update order
            $order->update([
                'total_amount' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Update items
            $order->items()->delete();
            $order->items()->createMany($orderItems);

            // Add to status history
            $statusHistory = $order->status_history ?? [];
            $statusHistory[] = [
                'status' => $order->status,
                'notes' => 'Order details updated by admin',
                'changed_by' => Auth::id(),
                'changed_at' => now()
            ];
            $order->status_history = $statusHistory;
            $order->save();

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    protected function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        try {
            DB::transaction(function() use ($order, $request, $oldStatus, $newStatus) {
                // Update order status and notes
                $order->update([
                    'status' => $newStatus,
                    'notes' => $request->notes
                ]);

                // Handle stock management when cancelling order
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $product = $item->product;

                        // Restore general stock
                        $product->increment('stock', $item->quantity);

                        // Restore size-specific stock if applicable
                        if ($item->size && $product->size_stocks && isset($product->size_stocks[$item->size])) {
                            $sizeStocks = $product->size_stocks;
                            $sizeStocks[$item->size] += $item->quantity;
                            $product->update(['size_stocks' => $sizeStocks]);
                        }
                    }
                }

                // Handle stock management when un-cancelling order
                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $product = $item->product;

                        // Check general stock availability
                        if ($product->stock < $item->quantity) {
                            throw new \Exception("Insufficient general stock for product: {$product->name}");
                        }

                        // Check size-specific stock if applicable
                        if ($item->size && $product->size_stocks && isset($product->size_stocks[$item->size])) {
                            $availableSizeStock = $product->size_stocks[$item->size];
                            if ($availableSizeStock < $item->quantity) {
                                throw new \Exception("Insufficient stock for product: {$product->name} (Size: {$item->size}). Available: {$availableSizeStock}");
                            }
                        }

                        // Reduce general stock
                        $product->decrement('stock', $item->quantity);

                        // Reduce size-specific stock if applicable
                        if ($item->size && $product->size_stocks && isset($product->size_stocks[$item->size])) {
                            $sizeStocks = $product->size_stocks;
                            $sizeStocks[$item->size] -= $item->quantity;
                            $product->update(['size_stocks' => $sizeStocks]);
                        }
                    }
                }

                // Persist basic status/notes only (no status_history column in schema)
                $order->refresh();
            });

            if ($oldStatus !== 'completed' && $newStatus === 'completed' && $order->user) {
                /** @var LoyaltyService $loyalty */
                $loyalty = app(LoyaltyService::class);
                $loyalty->awardDeliveredOrderPoints($order->user, $order);
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function() use ($order) {
                // If order is not cancelled, restore product stock
                if ($order->status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $order->items()->delete();
                $order->delete();
            });

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Order::with(['user', 'items.product']);

            // Apply filters similar to index method
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $orders = $query->get();

            // Generate CSV or Excel file
            $headers = [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Status',
                'Total Amount',
                'Items Count',
                'Shipping Name',
                'Shipping Phone',
                'Shipping Address',
                'Notes',
                'Created Date',
                'Last Updated',
            ];

            $rows = $orders->map(function($order) {
                return [
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->status,
                    number_format($order->total_amount, 2),
                    $order->items->count(),
                    $order->shipping_name,
                    $order->shipping_phone,
                    $order->shipping_address,
                    $order->notes,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            // Return CSV response
            $callback = function() use ($headers, $rows) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);

                foreach ($rows as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders_' . date('Y-m-d') . '.csv"',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting orders: ' . $e->getMessage());
        }
    }
}
