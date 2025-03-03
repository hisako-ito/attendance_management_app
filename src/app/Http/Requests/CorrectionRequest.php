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
            'break_start' => 'nullable|array',
            'break_start.*' => 'nullable|date_format:H:i|after:start_time|before:end_time',
            'break_end' => 'nullable|array',
            'break_end.*' => 'nullable|date_format:H:i|after:break_start.*|before:end_time',
            'reason' => 'required',
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
            'break_start.*.date_format' => '休憩開始時間を「00:00」の形式で記入してください。',
            'break_start.*.after' => '休憩時間が勤務時間外です。',
            'break_start.*.before' => '休憩時間が勤務時間外です。',
            'break_end.*.date_format' => '休憩終了時間を「00:00」の形式で記入してください。',
            'break_end.*.after' => '休憩終了時間は休憩開始時間より後に設定してください。',
            'break_end.*.before' => '休憩時間が勤務時間外です。',
            'reason.required' => '備考を記入してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $breakStarts = $this->input('break_start', []);
            $breakEnds = $this->input('break_end', []);

            foreach ($breakStarts as $key => $breakStart) {
                if (isset($breakEnds[$key]) && $breakEnds[$key] < $breakStart) {
                    $validator->errors()->add("break_end.$key", '休憩開始時間は休憩終了時間より前に設定してください。');
                }
            }
        });
    }
}
