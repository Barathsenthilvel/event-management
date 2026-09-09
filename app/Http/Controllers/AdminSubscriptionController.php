<?php

namespace App\Http\Controllers;

use App\Models\MemberSubscription;
use App\Models\PaymentTransaction;
use App\Services\RazorpayPaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $hasPaymentLinkCol = Schema::hasColumn('payment_transactions', 'razorpay_payment_link_id');
        $hasReferenceIdCol = Schema::hasColumn('payment_transactions', 'reference_id');

        $transactions = PaymentTransaction::query()
            ->with(['user:id,name,email,mobile', 'subscriptionPlan:id,subscription_type,payment_type'])
            ->when($q !== '', function ($query) use ($q, $hasPaymentLinkCol, $hasReferenceIdCol) {
                $query->where(function ($sub) use ($q, $hasPaymentLinkCol, $hasReferenceIdCol) {
                    $sub->where('id', $q)
                        ->orWhere('razorpay_payment_id', 'like', '%' . $q . '%')
                        ->orWhere('razorpay_order_id', 'like', '%' . $q . '%');

                    if ($hasPaymentLinkCol) {
                        $sub->orWhere('razorpay_payment_link_id', 'like', '%' . $q . '%');
                    }

                    if ($hasReferenceIdCol) {
                        $sub->orWhere('reference_id', 'like', '%' . $q . '%');
                    }

                    $sub->orWhereHas('user', function ($userQuery) use ($q) {
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
            ->get();

        $pendingCount = 0;
        if ($hasPaymentLinkCol) {
            $pendingCount = PaymentTransaction::query()
                ->where('status', 'pending')
                ->whereNotNull('razorpay_payment_link_id')
                ->count();
        }

        return view('admin.subscriptions.index', [
            'transactions' => $transactions,
            'subscriptions' => $subscriptions,
            'q' => $q,
            'status' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function sync(PaymentTransaction $transaction)
    {
        $service = app(RazorpayPaymentLinkService::class);
        $synced = $service->syncPaymentLinkStatus($transaction);

        if ($synced && $transaction->fresh()->status === 'successful') {
            return back()->with('success', 'Payment verified successfully from Razorpay and subscription activated for ' . ($transaction->user?->name ?? 'member') . '!');
        }

        return back()->with('info', 'Status checked with Razorpay: Transaction is still ' . $transaction->fresh()->status . '.');
    }

    public function syncAllPending()
    {
        $hasPaymentLinkCol = Schema::hasColumn('payment_transactions', 'razorpay_payment_link_id');
        if (! $hasPaymentLinkCol) {
            return back()->with('info', 'Payment link column is not yet migrated in the database.');
        }

        $pending = PaymentTransaction::query()
            ->where('status', 'pending')
            ->whereNotNull('razorpay_payment_link_id')
            ->get();

        $service = app(RazorpayPaymentLinkService::class);
        $count = 0;
        foreach ($pending as $tx) {
            if ($service->syncPaymentLinkStatus($tx)) {
                $count++;
            }
        }

        if ($count > 0) {
            return back()->with('success', "Checked Razorpay: {$count} pending transaction(s) verified and subscription(s) activated!");
        }

        return back()->with('info', 'Checked Razorpay: No pending transactions were found to be paid.');
    }
}
