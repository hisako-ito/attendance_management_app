<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->getUserFromRoute();

        if (!$user) {
            return false;
        }

        if (!hash_equals((string) $user->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * メール認証処理を実行する
     */
    public function fulfill()
    {
        $user = $this->getUserFromRoute();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);
    }

    /**
     * ルートパラメータからユーザーを取得するヘルパーメソッド
     */
    protected function getUserFromRoute()
    {
        return User::find($this->route('id'));
    }
}
