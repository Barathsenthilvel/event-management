<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MemberAuthController extends Controller
{
    private const OTP_SESSION_KEY = 'member_otp';
    private const OTP_EXPIRES_SESSION_KEY = 'member_otp_expires_at';
    private const OTP_USER_SESSION_KEY = 'member_otp_user_id';

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('member.auth.register');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        return view('member.auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $request->identifier;
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $user = User::where($field, $identifier)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['The provided credentials are incorrect.'],
            ]);
        }

        $this->issueOtp($request, (int) $user->id);

        return redirect()->route('member.otp');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'profile_completed' => false,
        ]);

        $this->issueOtp($request, $user->id);

        return redirect()->route('member.otp');
    }

    public function showOtpForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        if (!$request->session()->has(self::OTP_USER_SESSION_KEY)) {
            return redirect()->route('member.register');
        }

        return view('member.auth.otp', [
            'maskedMobile' => $this->maskedMobile($request),
            'generatedOtp' => (string) $request->session()->get(self::OTP_SESSION_KEY, ''),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'size:4'],
        ]);

        $userId = (int) $request->session()->get(self::OTP_USER_SESSION_KEY);
        $otp = (string) $request->session()->get(self::OTP_SESSION_KEY);
        $expiresAt = (int) $request->session()->get(self::OTP_EXPIRES_SESSION_KEY, 0);

        if (!$userId || !$otp) {
            throw ValidationException::withMessages([
                'code' => ['Session expired. Please register again.'],
            ]);
        }

        if (time() > $expiresAt) {
            throw ValidationException::withMessages([
                'code' => ['OTP expired. Please resend OTP.'],
            ]);
        }

        if ($data['code'] !== $otp) {
            throw ValidationException::withMessages([
                'code' => ['Invalid OTP.'],
            ]);
        }

        $request->session()->forget([self::OTP_SESSION_KEY, self::OTP_EXPIRES_SESSION_KEY, self::OTP_USER_SESSION_KEY]);

        Auth::loginUsingId($userId);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard');
    }

    public function resendOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('member.dashboard');
        }

        $userId = (int) $request->session()->get(self::OTP_USER_SESSION_KEY);
        if (!$userId) {
            return redirect()->route('member.register');
        }

        $this->issueOtp($request, $userId);

        return back()->with('success', 'OTP resent successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }

    private function issueOtp(Request $request, int $userId): void
    {
        $otp = (string) random_int(1000, 9999);

        $request->session()->put(self::OTP_USER_SESSION_KEY, $userId);
        $request->session()->put(self::OTP_SESSION_KEY, $otp);
        $request->session()->put(self::OTP_EXPIRES_SESSION_KEY, time() + (5 * 60));

        // For now: OTP is auto-generated. Later you can send via SMS/email provider.
    }

    private function maskedMobile(Request $request): string
    {
        $userId = (int) $request->session()->get(self::OTP_USER_SESSION_KEY);
        if (!$userId) {
            return '********';
        }

        $mobile = (string) optional(User::find($userId))->mobile;
        if (!$mobile) {
            return '********';
        }

        $digits = preg_replace('/\D+/', '', $mobile) ?? '';
        if (strlen($digits) <= 4) {
            return str_repeat('*', max(0, strlen($digits) - 1)) . substr($digits, -1);
        }

        return str_repeat('*', strlen($digits) - 4) . substr($digits, -4);
    }
}

