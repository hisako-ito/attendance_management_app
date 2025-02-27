<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Providers\RouteServiceProvider;
use Tests\TestCase;

class AdminUserLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーションテスト
     *
     * @dataProvider dataAdminUserLogin
     */
    public function testAdminUserLoginRequestValidation($keys, $values, $expect, $expectedErrors = [])
    {
        $data = array_combine($keys, $values);

        $response = $this->postJson('/admin/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        if ($expect) {
            $response->assertStatus(302);
        } else {
            $response->assertStatus(422);
            $response->assertJsonValidationErrors($expectedErrors);
        }
    }

    public function dataAdminUserLogin()
    {
        return [
            '管理者ログイン成功' => [
                ['email', 'password'],
                ['admin1@gmail.com', 'password'],
                True,
                [],
            ],
            '名前未入力エラー' => [
                ['email', 'password'],
                [null, 'password'],
                false,
                ['email' => 'メールアドレスを入力してください'],
            ],
            'password未入力エラー' => [
                ['email', 'password'],
                ['admin1@gmail.com', ''],
                false,
                ['password' => 'パスワードを入力してください'],
            ],
        ];
    }

    public function testFailedAdminUserLogin()
    {
        $data = [
            'email' => 'general1@gmail.com',
            'password' => 'password',
        ];

        $response = $this->post('/admin/login', $data, ['X-CSRF-TOKEN' => csrf_token()]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHas(['errors']);
        $errors = session('errors')->getBag('default')->get('email');
        $this->assertContains('ログイン情報が登録されていません', $errors);
    }

    public function testSuccessfulAdminUserLogin()
    {
        $adminUser = Admin::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/attendance/list');

        $this->assertAuthenticatedAs($adminUser, 'admin');
    }
}
