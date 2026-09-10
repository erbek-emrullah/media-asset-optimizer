<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // dogru bilgilerle giris yapinca token donmeli
    public function test_login_basarili()
    {
        $user = User::create([
            'name' => 'Test Editor',
            'email' => 'editor@test.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'editor@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'token_type']);
    }

    // yanlis sifre ile 401 donmeli
    public function test_login_yanlis_sifre()
    {
        $user = User::create([
            'name' => 'Test Editor',
            'email' => 'editor@test.com',
            'password' => '123456',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'editor@test.com',
            'password' => 'yanlissifre',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'email veya sifre yanlis']);
    }

    // email ve sifre bos gonderilirse 422 donmeli
    public function test_login_eksik_bilgi()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
    }
}
