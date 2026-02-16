<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect('/')->with('status', 'Registration successful');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = (bool) $request->input('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('status', 'Logged in');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Logged out');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showVerifyOtp(Request $request)
    {
        $email = $request->query('email');
        return view('auth.verify-otp', compact('email'));
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        $otp = $request->query('otp');
        return view('auth.reset-password', compact('email', 'otp'));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Delete any existing OTPs for this email
        PasswordResetOtp::where('email', $email)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP
        PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send notification
        $user = User::where('email', $email)->first();
        $user->notify(new PasswordResetOtpNotification($otp));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'OTP sent to your email']);
        }

        return redirect()->route('password.verify', ['email' => $email])->with('status', 'OTP sent to your email');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid or expired OTP'], 400);
            }
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'OTP verified', 'reset_token' => $otpRecord->id]);
        }

        return redirect()->route('password.reset', ['email' => $request->email, 'otp' => $request->otp])->with('status', 'OTP verified');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid or expired OTP'], 400);
            }
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the OTP
        $otpRecord->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Password reset successfully']);
        }

        return redirect()->route('login')->with('status', 'Password reset successfully. Please login with your new password.');
    }
}
