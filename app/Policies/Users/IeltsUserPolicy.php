<?php

namespace App\Policies\Users;

use App\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class IeltsUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function index(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_USERS_STUDENTS_IELTS_COURSES_INDEX);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_USERS_STUDENTS_IELTS_COURSES_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\IETLSCoursesIeltsUser  $iETLSCoursesIeltsUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_USERS_STUDENTS_IELTS_COURSES_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\IETLSCoursesIeltsUser  $iETLSCoursesIeltsUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_USERS_STUDENTS_IELTS_COURSES_DELETE);
    }
}