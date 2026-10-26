<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoyaltyPointTransaction;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query
            ->withCount(['loyaltyTransactions as total_transactions'])
            ->orderByDesc('loyalty_points_balance')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'total_points' => User::sum('loyalty_points_balance'),
            'total_transactions' => LoyaltyPointTransaction::count(),
        ];

        return view('admin.loyalty.index', compact('users', 'stats'));
    }

    public function show(User $user)
    {
        $transactions = $user->loyaltyTransactions()->latest()->paginate(25);
        return view('admin.loyalty.show', compact('user', 'transactions'));
    }
}
