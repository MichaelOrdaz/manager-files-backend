<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

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
        return [
            'email' => [
                'required',
                'email',
                'min:10',
                'max:100',
                Rule::unique('users')->ignore($user)->whereNull('deleted_at'),
            ],
            'nombre' => 'required|min:2|max:100',
            'paterno' => 'required|min:2|max:100',
            'materno' => 'required|min:2|max:100',
            'celular' => 'required|min:7|max:20',
            'password' => 'required|min:2|max:60',
            'imagen' => "nullable|image|max:{$MAX_FILE_SIZE}",
            'role_id' => 'required|integer',
            'departamento_id' => 'nullable|integer',
        ];
    }

    public function getData()
    {
        $validated = $this->validated();

        $validated['role'] = Role::findOrFail($validated['role_id']);
        $validated['departamento'] = Department::find($validated['departamento_id']);
        
        $validated['password'] = Hash::make($validated['password']);

        if ($this->hasFile('imagen') && $this->file('imagen')->isValid()) {
            $path = $this->file('imagen')->store('profiles', 'public');
            $validated['imagen'] = $path;
        }
        return $validated;
    }

}