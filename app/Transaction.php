<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'summary_id',
        'seller_id',
        'branch_id',
        'year',
        'subject_id',
        'cost',
    ];
}
