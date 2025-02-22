<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;
use App\Http\Requests\RegisterRequest;

class UserRegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**
     * カスタムリクエストのバリデーションテスト
     *
     * @param array $keys 項目名の配列
     * @param array $values 値の配列
     * @param bool $expect 期待値 (true: バリデーションOK、false: バリデーションNG)
     * @param array $expectedErrors 期待するエラーメッセージの配列
     * @dataProvider dataUserRegistration
     */

    public function testRegisterRequest(array $keys, array $values, bool $expect, array $expectedErrors = [])
    {
        $dataList = array_combine($keys, $values);
        $request = new RegisterRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = Validator::make($dataList, $rules, $messages);
        $result = $validator->passes();
        $this->assertEquals($expect, $result);

        if (!$result) {
            $errors = $validator->errors()->toArray();
            foreach ($expectedErrors as $field => $expectedMessage) {
                $this->assertArrayHasKey($field, $errors);
                $this->assertContains($expectedMessage, $errors[$field]);
            }
        }
    }

    public function dataUserRegistration()
    {
        return [
            '名前未入力エラー' => [
                ['name', 'email', 'password', 'password_confirmation'],
                [null, 'taro@example.com', 'password123', 'password123'],
                false,
                ['name' => 'お名前を入力してください']
            ],
            'email未入力エラー' => [
                ['name', 'email', 'password', 'password_confirmation'],
                ['山田太郎', null, 'password123', 'password123'],
                false,
                ['email' => 'メールアドレスを入力してください']
            ],
            'password最小文字数未満エラー' => [
                ['name', 'email', 'password', 'password_confirmation'],
                ['山田太郎', 'taro@example.com', 'passwor', 'passwor'],
                false,
                ['password' => 'パスワードは8文字以上で入力してください']
            ],
            'password不一致エラー' => [
                ['name', 'email', 'password', 'password_confirmation'],
                ['山田太郎', 'taro@example.com', 'password', 'password123'],
                false,
                ['password' => 'パスワードと一致しません']
            ],
            'password未入力エラー' => [
                ['name', 'email', 'password', 'password_confirmation'],
                ['山田太郎', 'taro@example.com', '', ''],
                false,
                ['password' => 'パスワードを入力してください']
            ],
        ];
    }

    public function testSuccessfulUserRegistration()
    {
        $data = [
            'name' => '山田太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'email' => 'taro@example.com',
        ]);

        $response->assertRedirect('/email/verify');
    }
}
