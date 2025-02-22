<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーションテスト
     *
     * @dataProvider dataUserLogin
     */
    public function testLoginRequestValidation($keys, $values, $expect, $expectedErrors = [])
    {
        $data = array_combine($keys, $values);

        $response = $this->postJson('/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        if ($expect) {
            $response->assertStatus(302);
        } else {
            $response->assertStatus(422);
            $response->assertJsonValidationErrors($expectedErrors);
        }
    }

    public function dataUserLogin()
    {
        return [
            '一般ユーザーログイン成功' => [
                ['email', 'password'],
                ['general1@gmail.com', 'password'],
                True,
                [],
            ],
            '名前必須エラー' => [
                ['email', 'password'],
                [null, 'password'],
                false,
                ['email' => 'メールアドレスを入力してください'],
            ],
            'password必須エラー' => [
                ['email', 'password'],
                ['general1@gmail.com', ''],
                false,
                ['password' => 'パスワードを入力してください'],
            ],
        ];
    }

    public function testFailedUserLogin()
    {
        User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'hanako@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas(['errors']);
        $errors = session('errors')->getBag('default')->get('email');
        $this->assertContains('ログイン情報が登録されていません', $errors);
    }

    public function testSuccessfulUserLogin()
    {
        $user = User::factory()->create([
            'email' => 'general2@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => "general2@gmail.com",
            'password' => "password",
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }
}
