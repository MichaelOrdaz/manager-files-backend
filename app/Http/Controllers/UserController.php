<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function permissions ($id)
    {
        $user = User::find($id);
        return $user->getAllPermissions()
            ->whereNull('is_view');
    }

    public function views ($id)
    {
        $user = User::find($id);
        return $user->getAllPermissions()
            ->whereNotNull('is_view');
    }
    
    public function roles ($id)
    {
        $user = User::find($id);
        return $user->getRoleNames();
    }

    
}
