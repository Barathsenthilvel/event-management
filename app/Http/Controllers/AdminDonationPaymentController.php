<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationPayment;
use Illuminate\Http\Request;

class AdminDonationPaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');
        $donationId = (int) $request->query('donation_id', 0);
        $viewMode = $request->query('view', 'table');
        if (! in_array($viewMode, ['table', 'cards'], true)) {
            $viewMode = 'table';
        }

        $payments = DonationPayment::query()
            ->with(['donation:id,purpose', 'user:id,name,email'])
            ->when($donationId > 0, fn ($query) => $query->where('donation_id', $donationId))
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

        $selectedDonation = $donationId > 0
            ? Donation::query()->select('id', 'purpose')->find($donationId)
            : null;

        $statsBase = DonationPayment::query()->when($donationId > 0, fn ($query) => $query->where('donation_id', $donationId));
        $successfulBase = (clone $statsBase)->where('status', 'successful');
        $memberPaidCount = (clone $successfulBase)->whereNotNull('user_id')->count();
        $guestPaidCount = (clone $successfulBase)->whereNull('user_id')->count();

        return view('admin.donations.payments-index', [
            'payments' => $payments,
            'q' => $q,
            'status' => $status,
            'donationId' => $donationId,
            'selectedDonation' => $selectedDonation,
            'viewMode' => $viewMode,
            'memberPaidCount' => $memberPaidCount,
            'guestPaidCount' => $guestPaidCount,
        ]);
    }
}

