<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'value' => 'required',
        ];

        $type = $this->input('type');
        $key = $this->input('key');

        if ($type == 2) {
            switch ($key) {
                case 'website-auth-background':
                    $rules['value'] = 'required|file|mimes:jpeg,jpg,webp|max:600';
                    break;

                case 'website-logo':
                    $rules['value'] = 'required|file|mimes:svg|max:600';
                    break;

                case 'website-favicon':
                    $rules['value'] = 'required|file|mimes:ico|max:600';
                    break;

                case 'website-dashboard-logo':
                    $rules['value'] = 'required|file|mimes:svg|max:600';
                    break;

                default:
                    $rules['value'] = 'required|file|image|max:600|mimetypes:image/*';
                    break;
            }
        } elseif ($type == 1) {
            if ($key === 'backend-prefix') {
                $rules['value'] = 'required|regex:/^[a-z0-9\-]+$/|max:255';
            } else {
                $rules['value'] = 'required|string|max:15';
            }
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
