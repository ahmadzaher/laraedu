<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Subject;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatistics extends Controller
{
    public function users()
    {
        $user = auth()->user();
        $current_date = date("Y-m-d");
        $last_seven_day = date('Y-m-d', strtotime("$current_date - 7 days"));

        $seven_days_users = DB::select("SELECT COUNT('id') as users, DATE_FORMAT(created_at,'%Y-%m-%d') as created_at FROM `users` WHERE DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . "$last_seven_day' group by DATE_FORMAT(created_at, '%Y-%m-%d')");
        $seven_days_user_chart_label = array();
        $seven_days_user_chart_data = array();
        $seven_days_user_gain = 0;
        if(!empty($seven_days_users))
        {
            foreach($seven_days_users as $value)
            {
                array_push($seven_days_user_chart_label, date("jS M y",strtotime($value->created_at)));
                array_push($seven_days_user_chart_data, $value->users);
                $seven_days_user_gain = $seven_days_user_gain + $value->users;
            }
        }

        $number_of_students = User::leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('school_classes', 'school_classes.id', '=', 'users.class_id')
            ->leftJoin('school_sections', 'school_sections.id', '=', 'users.section_id')
            ->where('roles.slug', '=', 'student')
            ->count();

        $number_of_teachers = User::leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('school_classes', 'school_classes.id', '=', 'users.class_id')
            ->leftJoin('school_sections', 'school_sections.id', '=', 'users.section_id')
            ->where('roles.slug', '=', 'teacher')
            ->count();

        $number_of_staffs = User::leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('roles.slug', '!=', 'student')
            ->where('roles.slug', '!=', 'teacher')
            ->orWhere('roles.slug', null)
            ->count();
        $number_of_subjects = Subject::latest()->count();
        // return view('home', compact('number_of_staffs', 'number_of_students', 'number_of_teachers', 'number_of_subjects'));

        return response()->json([
            'seven_days_user_statistics' => [
                'seven_days_user_chart_label' => $seven_days_user_chart_label,
                'seven_days_user_chart_data' => $seven_days_user_chart_data,
                'seven_days_user_gain' => $seven_days_user_gain,
            ],
            'number_of_staffs' => $number_of_staffs,
            'number_of_students' => $number_of_students,
            'number_of_teachers' => $number_of_teachers,
            'number_of_subjects' => $number_of_subjects
        ], 200);


    }
}