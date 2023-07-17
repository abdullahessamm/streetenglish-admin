<?php

namespace App\Policies\Courses;

use App\Admin;
use App\Models\IeltsCourse;
use Illuminate\Auth\Access\HandlesAuthorization;

class IeltsCoursesPolicy
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
        return $admin->hasAbility(Admin::ABILITIES_COURSES_IELTS_INDEX);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_IELTS_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\IeltsCourse  $ieltsCourse
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_IELTS_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\IeltsCourse  $ieltsCourse
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_IELTS_DELETE);
    }
}