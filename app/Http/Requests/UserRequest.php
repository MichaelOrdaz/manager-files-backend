<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        $MB = 1024;
        $MAX_FILE_SIZE = (8 * $MB);

        $user = User::find($this->route('user_id'));
        $rules = [
            'email' => [
                'required',
                'email',
                'min:10',
                'max:100',
                Rule::unique('users')->ignore($user)->whereNull('deleted_at'),
            ],
            'name' => 'required|min:2|max:100',
            'lastname' => 'required|min:2|max:100',
            'second_lastname' => 'required|min:2|max:100',
            'phone' => 'required|min:7|max:20',
            'image_file' => "nullable|image|max:{$MAX_FILE_SIZE}",
            'role_id' => 'required|integer',
            'department_id' => 'nullable|integer',
            'password' => [Password::defaults()],
        ];

        if (is_null($this->route('user_id'))) {
            $rules['password'][] = 'required';
        } else {
            $rules['password'][] = 'nullable';
        }
        return $rules;
    }

    public function getData()
    {
        $validated = $this->validated();
        
        if (isset($validated['password']))
            $validated['password'] = Hash::make($validated['password']);
        else
            unset($validated['password']);

        if ($this->hasFile('image_file') && $this->file('image_file')->isValid()) {
            $path = $this->file('image_file')->store('profiles', 'public');
            $validated['image'] = $path;
        }
        return $validated;
    }

}