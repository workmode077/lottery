<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'enquire_now_mail' => 'required|email',
            'book_test_drive_mail' => 'required|email',
            'contact_us_service_mail' => 'required|email',
            'service_mail' => 'required|email',
            'offer_mail' => 'required|email',
            'product_mail' => 'required|email',
            'newsletter_mail' => 'required|email',
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
