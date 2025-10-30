<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class YearPostRequest extends FormRequest
{

    public function rules(): array
    {
        $currentYear = date('Y');
       $decodedId = base64_decode($this->route('year'));

        return [
            'year' => [
                'required',
                'max:' . $currentYear,
                'digits:4',
                Rule::unique('years', 'year')->ignore($decodedId, 'id'),
            ],
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
