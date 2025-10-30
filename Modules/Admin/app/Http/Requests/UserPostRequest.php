<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserPostRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $username = $this->input('username');

        if ($username && !str_starts_with($username, 'SA-')) {
            $this->merge(['username' => 'SA-' . $username]);
        }
    }

    public function rules(): array
    {
        $encodedId = $this->route('super_agent'); 
        $id = $encodedId ? base64_decode($encodedId) : null;

        return [
            'username' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!str_starts_with($value, 'SA-')) {
                        $fail('Username must start with SA-');
                    }
                },
                Rule::unique('users', 'username')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'plain_password' => 'required|string|min:6',
            'status' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
