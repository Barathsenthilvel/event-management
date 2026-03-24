<?php

namespace App\Http\Controllers;

use App\Models\MemberSubscription;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $transactions = PaymentTransaction::query()
            ->with(['user:id,name,email,mobile', 'subscriptionPlan:id,subscription_type,payment_type'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('id', $q)
                        ->orWhere('razorpay_payment_id', 'like', '%' . $q . '%')
                        ->orWhere('razorpay_order_id', 'like', '%' . $q . '%')
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', '%' . $q . '%')
                                ->orWhere('email', 'like', '%' . $q . '%')
                                ->orWhere('mobile', 'like', '%' . $q . '%');
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $subscriptions = MemberSubscription::query()
            ->whereIn('user_id', $transactions->pluck('user_id')->filter()->unique()->values())
            ->whereIn('razorpay_order_id', $transactions->pluck('razorpay_order_id')->filter()->unique()->values())
            ->get()
            ->keyBy(function (MemberSubscription $subscription) {
                return $subscription->user_id . '|' . $subscription->razorpay_order_id;
            });

        return view('admin.subscriptions.index', [
            'transactions' => $transactions,
            'subscriptions' => $subscriptions,
            'q' => $q,
            'status' => $status,
        ]);
    }
}
