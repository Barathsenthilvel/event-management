<?php

namespace App\Http\Controllers;

use App\Models\DonationPayment;
use Illuminate\Http\Request;

class AdminDonationPaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $payments = DonationPayment::query()
            ->with(['donation:id,purpose', 'user:id,name,email'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('donor_name', 'like', "%{$q}%")
                        ->orWhere('donor_email', 'like', "%{$q}%")
                        ->orWhere('donor_mobile', 'like', "%{$q}%")
                        ->orWhereHas('donation', function ($q2) use ($q) {
                            $q2->where('purpose', 'like', "%{$q}%");
                        });
                });
            })
            ->when($status !== 'all', fn ($q2) => $q2->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.donations.payments-index', [
            'payments' => $payments,
            'q' => $q,
            'status' => $status,
        ]);
    }
}

