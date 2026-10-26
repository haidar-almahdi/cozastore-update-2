<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $transactions = $user->loyaltyTransactions()->latest()->paginate(15);
        $balance = $user->loyalty_points_balance ?? 0;
        $walletBalance = (float) ($user->wallet_balance ?? 0);
        $blocks = intdiv($balance, 100);
        $notifications = $user->unreadNotifications()
            ->where('type', \App\Notifications\UserWalletConvertedNotification::class)
            ->latest()
            ->take(5)
            ->get();

        return view('shop.loyalty.index', compact('user', 'transactions', 'balance', 'walletBalance', 'blocks', 'notifications'));
    }
}
