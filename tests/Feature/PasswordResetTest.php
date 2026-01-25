<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PasswordResetOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_otp()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'OTP sent to your email']);

        $this->assertDatabaseHas('password_reset_otps', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, \App\Notifications\PasswordResetOtpNotification::class);
    }

    public function test_verify_otp()
    {
        $user = User::factory()->create();
        $otp = '123456';

        PasswordResetOtp::create([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/auth/verify-otp', [
            'email' => $user->email,
            'otp' => $otp,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'reset_token']);
    }

    public function test_reset_password()
    {
        $user = User::factory()->create();
        $otp = '123456';
        $newPassword = 'newpassword123';

        PasswordResetOtp::create([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'otp' => $otp,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset successfully']);

        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));

        $this->assertDatabaseMissing('password_reset_otps', [
            'email' => $user->email,
            'otp' => $otp,
        ]);
    }

    public function test_web_forgot_password_page_loads()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200)
            ->assertSee('Forgot Password');
    }

    public function test_web_verify_otp_page_loads()
    {
        $response = $this->get('/verify-otp?email=test@example.com');

        $response->assertStatus(200)
            ->assertSee('Verify OTP')
            ->assertSee('test@example.com');
    }

    public function test_web_reset_password_page_loads()
    {
        $response = $this->get('/reset-password?email=test@example.com&otp=123456');

        $response->assertStatus(200)
            ->assertSee('Reset Password');
    }
}
