<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Permissions\HasPermissionsTrait;
use Laravel\Passport\HasApiTokens;
// These two come from Media Library
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasPermissionsTrait;
    use Notifiable, HasApiTokens;
    use HasMediaTrait;

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'number',
        'class_id',
        'direction',
        'language',
        'branch_id',
        'subject_id',
        'year',
        'coins'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    public function SchoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function SchoolSection()
    {
        return $this->belongsTo(SchoolSection::class, 'section_id');
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function allocations()
    {
        return $this->belongsToMany(Permission::class, 'teacher_allocations');
    }
}
