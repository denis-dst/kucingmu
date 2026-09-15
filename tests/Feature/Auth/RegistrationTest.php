<?php

namespace Tests\Feature\Auth;

use App\Mail\RegistrationSuccessMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSessionHas('register_captcha');
        $response->assertSessionHas('register_captcha_question');
    }

    public function test_new_users_can_register(): void
    {
        Mail::fake();

        $response = $this->withSession(['register_captcha' => 12])
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'captcha' => 12,
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        Mail::assertSent(RegistrationSuccessMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_registration_fails_with_invalid_captcha(): void
    {
        Mail::fake();

        $response = $this->withSession(['register_captcha' => 12])
            ->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'captcha' => 99,
            ]);

        $response->assertSessionHasErrors(['captcha']);
        $this->assertGuest();
        Mail::assertNothingSent();
    }
}

