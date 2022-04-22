<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'confirmed', Password::defaults()],
            'new_password' => ['required', Password::defaults()],
        ];
    }

    public function getData()
    {
        $validated = $this->validated();
        $validated['newPasswordHashed'] = Hash::make($validated['new_password']);
        return $validated;
    }
}
