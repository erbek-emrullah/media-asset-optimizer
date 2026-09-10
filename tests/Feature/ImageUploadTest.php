<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    // gorsel yukleme basarili olmali
    public function test_upload_basarili()
    {
        Queue::fake();
        Storage::fake('local');

        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload', [
                'name' => 'test-gorsel',
                'image' => UploadedFile::fake()->image('foto.jpg', 800, 600),
            ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'gorsel yuklendi']);

        // veritabaninda kayit olusturuldu mu
        $this->assertDatabaseHas('images', ['name' => 'test-gorsel']);
    }

    // ayni dosya iki kere yuklenemez
    public function test_ayni_dosya_reddedilir()
    {
        Queue::fake();
        Storage::fake('local');

        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        $token = auth('api')->login($user);

        // ayni icerikli 2 dosya olustur
        $file1 = UploadedFile::fake()->create('foto1.jpg', 100, 'image/jpeg');
        $file2 = UploadedFile::fake()->create('foto2.jpg', 100, 'image/jpeg');

        // ilk yukleme
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload', [
                'name' => 'ilk',
                'image' => $file1,
            ]);

        // ikinci yukleme - reddedilmeli
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/upload', [
                'name' => 'ikinci',
                'image' => $file2,
            ]);

        $response->assertStatus(409);
    }

    // token olmadan yukleme yapilamaz
    public function test_token_olmadan_upload_reddedilir()
    {
        $response = $this->postJson('/api/upload', [
            'name' => 'test',
            'image' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertStatus(401);
    }

    // gorsel listesi sayfalanmis donmeli
    public function test_liste_sayfalama()
    {
        $user = User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        // 3 gorsel olustur
        for ($i = 1; $i <= 3; $i++) {
            Image::create([
                'name' => 'gorsel-' . $i,
                'mime_type' => 'image/jpeg',
                'folder' => '2026/09',
                'file_checksum' => 'checksum' . $i,
                'path' => 'originals/2026/09/test' . $i . '.jpg',
            ]);
        }

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/images');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'per_page',
            'total',
        ]);
        $response->assertJsonPath('total', 3);
    }
}
