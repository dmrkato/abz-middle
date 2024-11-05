<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60|min:2',
            'email' => 'required|string|email:rfc,dns',
            'phone' => 'required|string|regex:/^[\+]{0,1}380(\d{9})$/',
            'position_id' => 'required|integer|exists:positions,id|gte:1',
            'photo' => 'required|image|mimes:jpeg,jpg|max:5124',
        ];
    }

    /**
     * Prepare phone field to found record during db validation
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->get('phone');
        if (is_string($phone)
            && strlen($phone)
            && $phone[0] !== '+'
        ) {
            $phone = '+' . $phone;
            $this->merge([
                'phone' => $phone,
            ]);
        }
    }
}
