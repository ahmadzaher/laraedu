<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Subject;
use App\Traffic;
use App\Transaction;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function users(Request $request)
    {
        $period = $request->period;
        if(!$period)
            $period = '7 days';
        $current_date = date("Y-m-d");
        $last_seven_day = date('Y-m-d', strtotime("$current_date - $period"));
        if($period == 'all'){
            $last_seven_day = '2021-01-01';
        }
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
            'seven_days_user_statistics' => [
                'seven_days_user_chart_label' => $seven_days_user_chart_label,
                'seven_days_user_chart_data' => $seven_days_user_chart_data,
                'seven_days_user_gain' => $seven_days_user_gain,
            ],
        ], 200);

    }
    public function transactions(Request $request)
    {
        $period = $request->period;
        if(!$period)
            $period = '7 days';
        $current_date = date("Y-m-d");
        $last_seven_day = date('Y-m-d', strtotime("$current_date - $period"));
        if($period == 'all'){
            $last_seven_day = '2021-01-01';
        }
        $seven_days = DB::select("SELECT COUNT('id') as transactions, DATE_FORMAT(created_at,'%Y-%m-%d') as created_at FROM `transactions` WHERE DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . "$last_seven_day' group by DATE_FORMAT(created_at, '%Y-%m-%d')");
        $seven_days_chart_label = array();
        $seven_days_chart_data = array();
        $seven_days_gain = 0;

        if(!empty($seven_days))
        {
            foreach($seven_days as $value)
            {
                array_push($seven_days_chart_label, date("jS M y",strtotime($value->created_at)));
                array_push($seven_days_chart_data, $value->transactions);
                $seven_days_gain = $seven_days_gain + $value->transactions;
            }
        }

        return response()->json([
            'seven_days_statistics' => [
                'seven_days_chart_label' => $seven_days_chart_label,
                'seven_days_chart_data' => $seven_days_chart_data,
                'seven_days_gain' => $seven_days_gain,
            ],
        ], 200);

    }


    public function earnings(Request $request)
    {
        $period = $request->period;
        if(!$period)
            $period = '7 days';
        $current_date = date("Y-m-d");
        $last_seven_day = date('Y-m-d', strtotime("$current_date - $period"));
        if($period == 'all'){
            $last_seven_day = '2021-01-01';
        }
        $seven_days = DB::select("SELECT SUM(cost) as transactions, DATE_FORMAT(created_at,'%Y-%m-%d') as created_at FROM `transactions` WHERE DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . "$last_seven_day' group by DATE_FORMAT(created_at, '%Y-%m-%d')");
        $seven_days_chart_label = array();
        $seven_days_chart_data = array();
        $seven_days_gain = 0;

        if(!empty($seven_days))
        {
            foreach($seven_days as $value)
            {
                array_push($seven_days_chart_label, date("jS M y",strtotime($value->created_at)));
                array_push($seven_days_chart_data, $value->transactions);
                $seven_days_gain = $seven_days_gain + $value->transactions;
            }
        }

        return response()->json([
            'seven_days_statistics' => [
                'seven_days_chart_label' => $seven_days_chart_label,
                'seven_days_chart_data' => $seven_days_chart_data,
                'seven_days_gain' => $seven_days_gain,
            ],
        ], 200);

    }
    public function code(Request $request)
    {
        $period = $request->period;
        if(!$period)
            $period = '7 days';
        $current_date = date("Y-m-d");
        $last_seven_day = date('Y-m-d', strtotime("$current_date - $period"));
        if($period == 'all'){
            $last_seven_day = '2021-01-01';
        }
        $seven_days = DB::select("SELECT COUNT('id') as code, DATE_FORMAT(created_at,'%Y-%m-%d') as created_at FROM `codes` WHERE DATE_FORMAT(created_at, '%Y-%m-%d') >= '" . "$last_seven_day' group by DATE_FORMAT(created_at, '%Y-%m-%d')");
        $seven_days_chart_label = array();
        $seven_days_chart_data = array();
        $seven_days_gain = 0;

        if(!empty($seven_days))
        {
            foreach($seven_days as $value)
            {
                array_push($seven_days_chart_label, date("jS M y",strtotime($value->created_at)));
                array_push($seven_days_chart_data, $value->code);
                $seven_days_gain = $seven_days_gain + $value->code;
            }
        }

        return response()->json([
            'seven_days_statistics' => [
                'seven_days_chart_label' => $seven_days_chart_label,
                'seven_days_chart_data' => $seven_days_chart_data,
                'seven_days_gain' => $seven_days_gain,
            ],
        ], 200);

    }

    public function main(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        if(!$year)
            $year = date('Y');
        if(!$month)
            $month = date('m');
        $transactions = Transaction::select([DB::raw('count(id) as transaction'), 'created_at'])->whereYear('created_at', $year)->whereMonth('created_at', $month)->where(function ($query) use ($day){
            if($day)
                $query->whereDay('created_at', $day);
        });
        if($day)
            $transactions->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h')"));
        else
            $transactions->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $transactions = $transactions->get();
        $traffic = Traffic::select([DB::raw('count(id) as traffic'), 'created_at'])->where('type', 'traffic')->whereYear('created_at', $year)->whereMonth('created_at', $month)->where(function ($query) use ($day){
            if($day)
                $query->whereDay('created_at', $day);
        });
        if($day)
            $traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h')"));
        else
            $traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $traffic = $traffic->get();
        $login_traffic = Traffic::select([DB::raw('count(id) as traffic'), 'created_at'])->where('type', 'login')->whereYear('created_at', $year)->whereMonth('created_at', $month)->where(function ($query) use ($day){
            if($day)
                $query->whereDay('created_at', $day);
        });
        if($day)
            $login_traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h')"));
        else
            $login_traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $login_traffic = $login_traffic->get();
        $register_traffic = Traffic::select([DB::raw('count(id) as traffic'), 'created_at'])->where('type', 'register')->whereYear('created_at', $year)->whereMonth('created_at', $month)->where(function ($query) use ($day){
            if($day)
                $query->whereDay('created_at', $day);
        });
        if($day)
            $register_traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %h')"));
        else
            $register_traffic->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $register_traffic = $register_traffic->get();
        $register_traffic_data = $traffic_data = $login_traffic_data = $transactions_data = array();
        $register_traffic_gain = $traffic_gain = $login_traffic_gain = $transactions_gain = 0;
        $labels = array();
        $number_days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $number = $day ? 23 : $number_days_in_month;
        if(!empty($transactions))
        {
            for($i = $day ? 0 : 1; $i <= $number; $i++)
            {
                $label = date("jS M y",strtotime("$year-$month-$i"));
                if($day){
                    $label = $i < 10 ? '0' . "$i:00" : "$i:00";
                }
                array_push($labels, $label);
                $isset_transaction = $isset_traffic = $isset_login_traffic = $isset_register_traffic = false;
                foreach ($transactions as $value) {
                    if(date($day ? 'H' : 'j', strtotime($value->created_at)) == $i){
                        array_push($transactions_data, $value->transaction);
                        $transactions_gain = $transactions_gain + $value->transaction;
                        $isset_transaction = true;
                    }
                }
                foreach ($traffic as $value) {
                    if(date($day ? 'H' : 'j', strtotime($value->created_at)) == $i){
                        array_push($traffic_data, $value->traffic);
                        $traffic_gain = $traffic_gain + $value->traffic;
                        $isset_traffic = true;
                    }
                }
                foreach ($login_traffic as $value) {
                    if(date($day ? 'H' : 'j', strtotime($value->created_at)) == $i){
                        array_push($login_traffic_data, $value->traffic);
                        $login_traffic_gain = $login_traffic_gain + $value->traffic;
                        $isset_login_traffic = true;
                    }
                }
                foreach ($register_traffic as $value) {
                    if(date($day ? 'H' : 'j', strtotime($value->created_at)) == $i){
                        array_push($register_traffic_data, $value->traffic);
                        $register_traffic_gain = $register_traffic_gain + $value->traffic;
                        $isset_register_traffic = true;
                    }
                }
                if(!$isset_transaction)
                    array_push($transactions_data, 0);

                if(!$isset_traffic)
                    array_push($traffic_data, 0);

                if(!$isset_login_traffic)
                    array_push($login_traffic_data, 0);

                if(!$isset_register_traffic)
                    array_push($register_traffic_data, 0);

            }
        }

        return response()->json([
            'labels' => $labels,
            'transactions' => [
                'data' => $transactions_data,
                'gain' => $transactions_gain,
            ],
            'traffic' => [
                'data' => $traffic_data,
                'gain' => $traffic_gain,
            ],
            'login_traffic' => [
                'data' => $login_traffic_data,
                'gain' => $login_traffic_gain,
            ],
            'register_traffic' => [
                'data' => $register_traffic_data,
                'gain' => $register_traffic_gain,
            ],
            'number_of_days_in_month' => $number_days_in_month
        ], 200);

    }

    public function latest_users()
    {
        $users = User::latest()->limit(6)
            ->select([
                'users.*',
                DB::raw("(select count(id) from traffic where user_id = users.id AND type='traffic')  as traffic_count"),
                DB::raw("(select max(created_at) from traffic where user_id = users.id AND type='login') as last_login"),
            ])
            ->get();
        return response($users);
    }

    public function statistics(){

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
        return response([
            'number_of_staffs' => $number_of_staffs,
            'number_of_students' => $number_of_students,
            'number_of_teachers' => $number_of_teachers,
            'number_of_subjects' => $number_of_subjects
        ], 200);
    }
}
