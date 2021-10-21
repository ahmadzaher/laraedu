<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        $data = User::latest()
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('roles.slug', '!=', 'student')
            ->where('roles.slug', '!=', 'teacher')
            ->orWhere('roles.slug', null)
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number'
            )->paginate(10);
        foreach($data as $key => $staff)
        {
            $avatar = $staff->getFirstMediaUrl('avatars', 'thumb') != null ? url($staff->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

            $data[$key]['avatar'] = $avatar;
            unset($data[$key]['media']);
        }


        return response()->json($data, 200);
    }
}
