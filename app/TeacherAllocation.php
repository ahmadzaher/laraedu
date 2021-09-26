<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherAllocation extends Model
{
    protected $fillable = [
        'class_id', 'section_id', 'teacher_id'
    ];

    public function SchoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function SchoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function Teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
