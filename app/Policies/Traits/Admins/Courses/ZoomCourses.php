<?php

namespace App\Policies\Traits\Admins\Courses;

use App\Admin;

trait ZoomCourses
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function index(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_ZOOM_INDEX);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_ZOOM_CREATE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\ZoomCourse  $zoomCourse
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_ZOOM_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Admin  $admin
     * @param  \App\Models\ZoomCourse  $zoomCourse
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $admin)
    {
        return $admin->hasAbility(Admin::ABILITIES_COURSES_ZOOM_DELETE);
    }
}