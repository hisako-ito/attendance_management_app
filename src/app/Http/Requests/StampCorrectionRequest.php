<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required',
            'break_start.*' => [
                'nullable',
                'date_format:H:i',
                'before:break_end.*',
                function ($attribute, $value, $fail) {
                    $start_time = request()->input('start_time');
                    if (!empty($start_time) && strtotime($value) <= strtotime($start_time)) {
                        $fail('休憩時間が勤務時間外です。');
                    }
                },
            ],
            'break_end.*' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $end_time = request()->input('end_time');
                    if (!empty($end_time) && strtotime($value) >= strtotime($end_time)) {
                        $fail('休憩時間が勤務時間外です。');
                    }
                },
            ],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'start_time.before' => '出勤時間もしくは退勤時間が不適切な値です。',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'reason.required' => '備考を記入してください。',
        ];
    }
}
