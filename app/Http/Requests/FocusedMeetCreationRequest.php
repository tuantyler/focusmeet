<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FocusedMeetCreationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'allow_freely_join' => 'required|boolean',
            'enable_member_focus_view' => 'required|boolean',
            'enable_member_list_view' => 'required|boolean',
            'all_days' => 'required|boolean',
            'repeat_pattern' => 'required|integer|digits:1',
            'end_time' => 'required|date',
            'meeting_name' => 'required|string',
            'start_time' => 'required|date',
        ];
    }
}
