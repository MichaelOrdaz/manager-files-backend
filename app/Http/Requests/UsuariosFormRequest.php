<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuariosFormRequest extends FormRequest
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
        $user = User::find($this->route('usuario_id'));
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user),
            ],
            'remember_token' => 'nullable|string',
            'firebase_uid' => 'nullable|string',
            'role' => 'required',
            'activo' => 'nullable',
        ];

        return $rules;
    }

    /**
     * Get the request's data from the request.
     *
     *
     * @return array
     */
    public function getData()
    {
        $data = $this->only(['email', 'remember_token', 'firebase_uid', 'activo','role']);
        if($this->has('activo')){ $data['activo'] = filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN); }
        return $data;
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new \Illuminate\Http\Response([
            'errors' => $validator->errors()->all(),
            'success' => false,
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
