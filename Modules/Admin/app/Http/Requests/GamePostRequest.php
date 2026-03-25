<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GamePostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('game') ? base64_decode($this->route('game')) : null;

        return [
            'time' => [
                'required',
                'date_format:H:i',
                Rule::unique('games', 'time')->whereNull('deleted_at')->ignore($id),
            ],
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
