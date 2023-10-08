<?php

namespace App\Http\Requests\Api\AdminDashboard\Courses\Zoom\Privates;

use App\Models\ZoomCourses\ZoomCourse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('sanctum')->user()->can('update', ZoomCourse::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "instructor_id"         => ["integer", "exists:coaches,id"],
            "live_course_user_id"   => ["integer", "exists:live_course_users,id"],
            "sessions"              => ["array"],
            "sessions.*.id"         => ["required", "integer", "exists:zoom_course_sessions,id"],
            "sessions.*.time"       => ["nullable", "date", "after:now"],
            "sessions.*.duration"   => ["nullable", "integer", "min:1", "max:255"],
            "sessions.*.room_link"  => ["nullable", "url"]
        ];
    }
}
