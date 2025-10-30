<?php

namespace Modules\Admin\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Decode the admin ID from the route if available
        $adminId = $this->route('admin') ? base64_decode($this->route('admin')) : null;

        // Check if the admin's name is 'Admin' (applicable for update scenarios)
        $isAdmin = false;
        if (in_array($this->method(), ['PUT', 'PATCH']) && $adminId) {
            $admin = Admin::find($adminId);
            $isAdmin = $admin && $admin->id === Admin::min('id');
        }

        $rules = [
            'name' => 'required|min:3|max:25',
            'email' => 'required|email|unique:admins,email' . ($adminId ? ',' . $adminId : ''),
            'password' => [
                'required_if:method,POST',
                'nullable',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/',
            ],
            'password_confirmation' => [
                'required_if:method,POST',
                'nullable',
                'same:password',
            ],
        ];

        // Apply additional rules only if the admin's name is not 'Admin'
        if (!$isAdmin) {
            $rules['role_id'] = 'required|array';
            $rules['role_id.*'] = 'exists:roles,id';
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
