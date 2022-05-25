<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class UserPermissionPostRequest extends FormRequest
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
        $permission = Dixa::ANALYST_PERMISSIONS;
        return [
            'users' => 'array|nullable',
            'users.*.id' => 'required|integer',
            'users.*.permission' => "nullable|in:" . implode(',', $permission),
        ];
    }

    public function getData()
    {
        $validated = $this->validated();
        if (!isset($validated['users']) || empty($validated['users'])) {
            return collect([]);
        }
        $data = collect($validated['users']);
        $data = $data->map(function ($item) {
            $item['user'] = User::find($item['id']);
            return $item;
        })->filter(function ($item) {
            return $item['user'] && $item['user']->department_id === $this->user()->department_id;
        });
        return $data;
    }
}
