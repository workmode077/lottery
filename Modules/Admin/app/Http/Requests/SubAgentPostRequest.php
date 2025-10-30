<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubAgentPostRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $username = $this->input('username');

        if ($username && !str_starts_with($username, 'SUB-')) {
            $this->merge(['username' => 'SUB-' . $username]);
        }
    }

   public function rules(): array
    {
        $encodedId = $this->route('sub_agent'); 
        $id = $encodedId ? base64_decode($encodedId) : null;

        return [
            'username' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!str_starts_with($value, 'SUB-')) {
                        $fail('Username must start with SUB-');
                    }
                },
                Rule::unique('users', 'username')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'parent_id' => 'required|exists:users,id',
            'plain_password' => 'required|string|min:6',
            'status' => 'required|boolean',
        ];
    }

}
