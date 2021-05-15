<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
        return response()->json([
            'seven_days_user_chart_label' => $seven_days_user_chart_label,
            'seven_days_user_chart_data' => $seven_days_user_chart_data,
            'seven_days_user_gain' => $seven_days_user_gain
        ], 200);


    }
}
