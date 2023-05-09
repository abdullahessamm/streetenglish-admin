<?php

namespace App\Models\Students;

use App\Models\EnrolledStudents\EnrolledStudentForOnlineCourse;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'image'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function course()
    {
        return $this->hasOne(EnrolledStudentForOnlineCourse::class, 'user_id', 'id');
    }
}
