<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function getUsers()
    {
        $data = User::latest()
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->where('roles.slug', '=', 'teacher')
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number',
                'department_id',
                'roles.slug as role',
                'departments.name as department_name'
            )->paginate(10);
        foreach($data as $key => $teacher)
        {
            $avatar = $teacher->getFirstMediaUrl('avatars', 'thumb') != null ? url($teacher->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

            $data[$key]['avatar'] = $avatar;
            unset($data[$key]['media']);
        }


        return response()->json($data, 200);
    }
}
