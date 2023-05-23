<?php

namespace App\Http\Controllers\Api\Users\Students;

use App\Admin;
use App\Models\ZoomCourses\ZoomCourseUser;

class ZoomCourseStudentsController extends StudentController
{

    protected string $modelClassName = ZoomCourseUser::class;

    public function __construct()
    {
        $this->abilities['index'] = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_INDEX);
        $this->abilities['store'] = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_CREATE);
        $this->abilities['show']  = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_INDEX);
        $this->abilities['update'] = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_UPDATE);
        $this->abilities['destroy'] = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_DELETE);
        $this->abilities['updateProfilePic'] = auth('sanctum')->user()->tokenCan(Admin::ABILITIES_USERS_STUDENTS_ZOOM_COURSES_UPDATE);
    }
}
