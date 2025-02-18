<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CorrectionRequest extends FormRequest
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
        return [
            'start_time' => 'required|date_format:H:i|before:end_time',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required',
            'break_start.*' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $start_time = request()->input('start_time');
                    $index = explode('.', $attribute)[1];
                    $break_end = request()->input('break_end')[$index] ?? null;

                    if (!empty($start_time) && strtotime($value) <= strtotime($start_time)) {
                        $fail('休憩時間が勤務時間外です。');
                    }

                    if ($break_end && strtotime($value) >= strtotime($break_end)) {
                        $fail('休憩開始時間は休憩終了時間より前にしてください。');
                    }
                },
            ],
            'break_end.*' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $end_time = request()->input('end_time');
                    $index = explode('.', $attribute)[1];
                    $break_start = request()->input('break_start')[$index] ?? null;

                    if (!empty($end_time) && strtotime($value) >= strtotime($end_time)) {
                        $fail('休憩時間が勤務時間外です。');
                    }

                    if ($break_start && strtotime($value) <= strtotime($break_start)) {
                        $fail('休憩終了時間は休憩開始時間より後にしてください。');
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間を記入してください。',
            'start_time.date_format' => '出勤時間を「00:00」の形式で記入してください。',
            'start_time.before' => '出勤時間もしくは退勤時間が不適切な値です。',
            'end_time.required' => '退勤時間を記入してください。',
            'end_time.date_format' => '退勤時間を「00:00」の形式で記入してください。',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です。',
            'reason.required' => '備考を記入してください。',
        ];
    }
}
