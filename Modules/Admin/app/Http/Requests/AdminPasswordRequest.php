<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AdminPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth('admin')->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/',
            ],
            'password_confirmation' => 'required|same:password',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
