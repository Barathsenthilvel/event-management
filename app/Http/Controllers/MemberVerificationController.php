<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MemberIdCardService;
use Illuminate\Http\Request;

class MemberVerificationController extends Controller
{
    public function verify(Request $request, ?string $code = null)
    {
        $searchCode = trim($code ?? $request->query('code', ''));

        $member = null;
        $memberCodeFormatted = null;
        $activeSubscription = null;
        $idCardService = app(MemberIdCardService::class);
        $cardUrls = null;

        if (!empty($searchCode)) {
            // Extract numerical ID from formats like:
            // GNAT-9715-0004 -> 4
            // GNAT-000004 -> 4
            // 9715-0004 -> 4
            // 4 -> 4
            $idCandidate = null;

            if (preg_match('/(?:GNAT-)?(?:9715-)?0*(\d+)/i', $searchCode, $matches)) {
                $idCandidate = (int) $matches[1];
            } elseif (is_numeric($searchCode)) {
                $idCandidate = (int) $searchCode;
            }

            if ($idCandidate) {
                $member = User::with(['designation', 'activeSubscription.plan'])
                    ->where('id', $idCandidate)
                    ->first();
            }

            // Fallback search by email or mobile if direct ID match was not found
            if (!$member) {
                $member = User::with(['designation', 'activeSubscription.plan'])
                    ->where('email', $searchCode)
                    ->orWhere('mobile', $searchCode)
                    ->first();
            }
        }

        if ($member) {
            $memberCodeFormatted = $idCardService->memberCode($member);
            $activeSubscription = $member->activeSubscription;
            $cardUrls = $idCardService->getCardUrls($member);
        }

        return view('verify-member', [
            'searchCode' => $searchCode,
            'member' => $member,
            'memberCodeFormatted' => $memberCodeFormatted,
            'activeSubscription' => $activeSubscription,
            'cardUrls' => $cardUrls,
            'verifiedAt' => now(),
        ]);
    }
}
