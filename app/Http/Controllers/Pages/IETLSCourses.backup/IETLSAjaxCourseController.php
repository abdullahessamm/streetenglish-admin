<?php

namespace App\Http\Controllers\Pages\IETLSCourses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\IETLSCourses\IETLSCourse;
use App\Models\IETLSCourses\IETLSCourseContent;
use App\Models\IETLSCourses\IETLSCourseCouponDiscount;
use App\Models\IETLSCourses\IETLSCourseDiscount;
use App\Models\IETLSCourses\IETLSCourseLesson;
use App\Models\IETLSCourses\IETLSCourseInstructor;

class IETLSAjaxCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $student = IETLSCourse::query();

        return Datatables::of($student)
        ->editColumn('name', function ($student) {
            return '<a href="'.route('ietls-course.show', [$student->slug]).'">'.$student->name.'</a>';
        })
        ->editColumn('created_at', function ($student) {
            return date("Y-m-d h:i:s a", strtotime($student->created_at));
        })
        ->rawColumns(['name'])
        ->make(true);
    }

    public function create(Request $request)
    {
        $ietls_course_name = $request->input('ietls_course_name');
        $duration = $request->input('duration');
        $level = $request->input('level');
        $language = $request->input('language');
        $choose_price_option = $request->input('choose_price_option');
        $isFree = $request->input('isFree') != null ? $request->input('isFree') : null;
        $price = $request->input('price') != null ? $request->input('price') : null;
        $description = $request->input('description');
        $thumbnail = $request->file('thumbnail');
        $banner = null;
        $choose_media = $request->input('choose_media');
        $image = null;
        $video_url = $request->input('video_url');
        $video_intro_type = $request->input('video_intro_type');
        $instructors = $request->input('coaches') != null ? $request->input('coaches') : null;
        $slug = $this->slugify($ietls_course_name);

        // check if at least one instructor choosen
        if($instructors == null)
        {
            $this->errorMsg("You must choose at least one instructor");
        }

        // check if banner is uploaded
        if($request->hasFile('banner'))
        {
            $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('banner')->getClientOriginalExtension(), "Banner image extension is not allowed");

            $banner = 'banner.'.$request->file('banner')->getClientOriginalExtension();
        }

        // check if intro image is uploaded
        if($request->hasFile('image'))
        {
            $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('image')->getClientOriginalExtension(), "Intro image extension is not allowed");

            $image = 'image.'.$request->file('image')->getClientOriginalExtension();
        }

        // check if thumbnail extension is allowed
        $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('thumbnail')->getClientOriginalExtension(), "Thumbnail image extension is not allowed");

        // check if intro video url is a youtube link
        if($video_url != null)
        {
            if($video_intro_type == 'vimeo')
            {
                $video_url = $this->parseVimeoURL($video_url);
            }

            if($video_intro_type == 'youtube')
            {
                $video_url = $this->parseYouTubeURL($video_url);
            }

            if($video_intro_type == 'drive')
            {
                $video_url = $video_url;
            }
        }

        $data = [
            'name' => $ietls_course_name, 
            'duration' => $duration, 
            'level' => $level, 
            'language' => $language, 
            'isFree' => $isFree,
            'media_intro' => $choose_media, 
            'video_url' => $video_url,
            'video_intro_type' => $video_intro_type,
            'image' => $image, 
            'banner' => $banner, 
            'thumbnail' => 'thumbnail.'.$thumbnail->getClientOriginalExtension(), 
            'description' => $description,
            'slug' => $slug,
        ];

        // upload course data
        $course = IETLSCourse::firstOrCreate(['name' => $ietls_course_name], $data);

        // check if course has discount
        if($choose_price_option == 'discount')
        {
            $discount = $request->input('discount') != null ? $request->input('discount') : null;

            IETLSCourseDiscount::firstOrCreate(['ietls_course_id' => $course->id], [
                'ietls_course_id' => $course->id,
                'price' => $price,
                'discount' => $discount,
            ]);
        }

        // check if course has coupon discount
        if($choose_price_option == 'coupon')
        {
            $discount = $request->input('discount') != null ? $request->input('discount') : null;
            $coupon = $request->input('coupon') != null ? $request->input('coupon') : null;

            IETLSCourseCouponDiscount::firstOrCreate(['ietls_course_id' => $course->id], [
                'ietls_course_id' => $course->id,
                'price' => $price,
                'discount' => $discount,
                'coupon' => $coupon,
            ]);
        }

        foreach($instructors as $instructor)
        {
            $IETLSCourseInstructor = new IETLSCourseInstructor();
            $IETLSCourseInstructor->coach_id = $instructor;
            $IETLSCourseInstructor->ietls_course_id = $course->id;
            $IETLSCourseInstructor->save();
        }

        $ietls_course_path = $this->getUniversalPath('public/images/ietls-courses/'.$course->id);

        $this->uploadFile($request, 'thumbnail', $ietls_course_path, 'thumbnail');
        $request->hasFile('banner') ? $this->uploadFile($request, 'banner', $ietls_course_path, 'banner') : null;
        $request->hasFile('image') ? $this->uploadFile($request, 'image', $ietls_course_path, 'image') : null;

        $this->successMsg("New  course has been added in our database");

        $this->redierctTo('ietls-course/show/'.$slug);
    }

    public function update(Request $request)
    {
        $ietls_course_id = $request->input('ietls_course_id');

        $course = IETLSCourse::where('id', $ietls_course_id)->first();

        $ietls_course_name = $request->input('ietls_course_name');
        $duration = $request->input('duration');
        $level = $request->input('level');
        $language = $request->input('language');
        $choose_price_option = $request->input('choose_price_option');
        
        $description = $request->input('description');
        $choose_media = $request->input('choose_media') == 'none' ? $course->media_intro : $request->input('choose_media');
        $video_url = $request->input('video_url');
        $video_intro_type = $request->input('video_intro_type');
        $slug = $this->slugify($ietls_course_name);

        // check if thumbnail extension is allowed
        if($request->hasFile('thumbnail'))
        {
            $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('thumbnail')->getClientOriginalExtension(), "Thumbnail image extension is not allowed");
            
            $thumbnail = 'thumbnail.'.$request->file('thumbnail')->getClientOriginalExtension();
        }
        else
        {
            $thumbnail = $course->thumbnail;
        }

        // check if banner is uploaded
        if($request->hasFile('banner'))
        {
            $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('banner')->getClientOriginalExtension(), "Banner image extension is not allowed");

            $banner = 'banner.'.$request->file('banner')->getClientOriginalExtension();
        }
        else
        {
            $banner = $course->banner;
        }

        // check if intro image is uploaded
        if($request->hasFile('image'))
        {
            $this->isFileExtAllowed(['jpg', 'jpeg', 'png'], $request->file('image')->getClientOriginalExtension(), "Intro image extension is not allowed");

            $image = 'image.'.$request->file('image')->getClientOriginalExtension();
        }
        else
        {
            $image = $course->image;
        }

        // check if intro video url is a youtube link
        if($video_url != null)
        {
            if($video_intro_type == 'vimeo')
            {
                $video_url = $this->parseVimeoURL($video_url);
            }

            if($video_intro_type == 'youtube')
            {
                $video_url = $this->parseYouTubeURL($video_url);
            }

            if($video_intro_type == 'drive')
            {
                $video_url = $video_url;
            }

            $image = null;
        }
        else
        {
            $video_url = $course->video_url;
        }

        switch($choose_price_option)
        {
            case 'free':
                $isFree = 1;
                $price = null;

                isset($course->discount) ? IETLSCourseDiscount::where('ietls_course_id', $course->id)->delete() : true;
                isset($course->coupon) ? IETLSCourseCouponDiscount::where('ietls_course_id', $course->id)->delete() : true;
            break;
            
            case 'price':
                $isFree = null;
                $price = $request->input('price');

                isset($course->discount) ? IETLSCourseDiscount::where('ietls_course_id', $course->id)->delete() : true;
                isset($course->coupon) ? IETLSCourseCouponDiscount::where('ietls_course_id', $course->id)->delete() : true;
            break;

            case 'discount':
                $isFree = null;
                $price = null;
                $discount = $request->input('discount');

                IETLSCourseDiscount::updateOrCreate(['ietls_course_id' => $course->id], [
                    'price' => $request->input('price'),
                    'discount' => $discount,
                ]);

                isset($course->coupon) ? IETLSCourseCouponDiscount::where('ietls_course_id', $course->id)->delete() : true;
            break;

            case 'coupon':
                $isFree = null;
                $price = null;
                $discount = $request->input('discount');
                $coupon = $request->input('coupon');

                IETLSCourseCouponDiscount::firstOrCreate(['ietls_course_id' => $course->id], [
                    'ietls_course_id' => $course->id,
                    'price' => $request->input('price'),
                    'discount' => $discount,
                    'coupon' => $coupon,
                ]);

                isset($course->discount) ? IETLSCourseDiscount::where('ietls_course_id', $course->id)->delete() : true;
            break;
        }

        $ietls_course_path = $this->getUniversalPath('public/images/ietls-courses/'.$course->id);

        $request->hasFile('thumbnail') ? $this->uploadFile($request, 'thumbnail', $ietls_course_path, 'thumbnail') : false;
        $request->hasFile('banner') ? $this->uploadFile($request, 'banner', $ietls_course_path, 'banner') : false;
        $request->hasFile('image') ? $this->uploadFile($request, 'image', $ietls_course_path, 'image') : false;
    
        IETLSCourse::where('id', $ietls_course_id)->update([
            'name' => $ietls_course_name, 
            'duration' => $duration, 
            'level' => $level, 
            'language' => $language,
            'isFree' => $isFree,
            'price' => $price,
            'media_intro' => $choose_media,
            'video_intro_type' => $video_intro_type,
            'video_url' => $video_url, 
            'image' => $image, 
            'banner' => $banner, 
            'thumbnail' => $thumbnail,
            'description' => $description,
            'slug' => $slug,
        ]);

        $this->successMsg("Course : ".$course->name." has been updated");

        $this->redierctTo('ietls-course/show/'.$slug);
    }

    public function delete(Request $request)
    {
        $ietls_course_id = $request->input('ietls_course_id');
        
        $ietls_course_images_path = $this->getUniversalPath('public/images/ietls-courses/'.$ietls_course_id);
        $ietls_course_contents_path = $this->getUniversalPath('public/uploads/courses/'.$ietls_course_id);

        if(IETLSCourse::where('id', $ietls_course_id)->delete())
        {
            file_exists($ietls_course_images_path) ? $this->deleteDir($ietls_course_images_path) : true;
            file_exists($ietls_course_contents_path) ? $this->deleteDir($ietls_course_contents_path) : true;
        }

        $this->successMsg("This course has been removed");
        $this->redierctTo('ietls-courses');
    }

    public function createContent(Request $request)
    {
        $ietls_course_id = $request->input('ietls_course_id');
        $content_name = $request->input('content_name');

        IETLSCourseContent::create([
            'ietls_course_id' => $ietls_course_id,
            'title' => $content_name,
        ]);
        
        $this->successMsg("New content has been added to this course");

        $this->reloadPage();
    }

    public function updateContentTitle(Request $request)
    {
        $content_id = $request->input('content_id');
        $content_title = $request->input('content_title');

        IETLSCourseContent::where('id', $content_id)->update([
            'title' => $content_title,
        ]);
    }
    
    public function updateContentDescription(Request $request)
    {
        $content_id = $request->input('content_id');
        $content_description = $request->input('content_description');

        IETLSCourseContent::where('id', $content_id)->update([
            'description' => $content_description,
        ]);
    }

    public function deleteContent(Request $request)
    {
        $content_id = $request->input('content_id');

        $courseContent = IETLSCourseContent::where('id', $content_id)->first();

        $ietls_course_id = $courseContent->belongsToCourse->id;

        $content_path = $this->getUniversalPath('public/uploads/courses/'.$ietls_course_id.'/content/'.$content_id);

        if($courseContent->delete())
        {
            file_exists($content_path) ? $this->deleteDir($content_path) : false;

            $this->successMsg("This content has been removed from this course");

            $this->reloadPage();
        }
    }

    public function createLesson(Request $request)
    {
        $ietls_course_id = $request->input('ietls_course_id');
        $content_id = $request->input('content_id');

        $lessons = $request->input('lessons');

        for($i = 0; $i < count($lessons); $i++)
        {
            $lesson_title = $lessons[$i]['lesson_title'];
            $video_type = $lessons[$i]['video_type'];
            
            if($video_type == 'vimeo')
            {
                $video_url = $this->parseVimeoURL($lessons[$i]['video_url']);
            }

            if($video_type == 'youtube')
            {
                $video_url = $this->parseYouTubeURL($lessons[$i]['video_url']);
            }

            if($video_type == 'drive')
            {
                $video_url = $lessons[$i]['video_url'];
            }

            $slug = md5(uniqid());

            IETLSCourseLesson::create([
                'ietls_course_content_id' => $content_id,
                'title' => $lesson_title,
                'video_url' => $video_url,
                'video_type' => $video_type,
                'slug' => $slug,
            ]);
        }

        $sucess_msg = count($lessons) > 1 ? 'تم اضافة دروس جديدة' : 'تم اضافة درس جديد';

        $this->successMsg($sucess_msg);

        $this->reloadPage();
    }


    public function updateLessonTitle(Request $request)
    {
        $lesson_id = $request->input('lesson_id');
        $lesson_title = $request->input('lesson_title');

        IETLSCourseLesson::where('id', $lesson_id)->update([
            'title' => $lesson_title,
        ]);
    }

    public function previewVideo(Request $request)
    {
        $lesson_id = $request->input('data')['lesson_id'];

        $courseLesson = IETLSCourseLesson::where('id', $lesson_id)->first();
        
        return view('pages.courses.preview-lesson')->with('courseLesson', $courseLesson);
    }

    public function updateVideo(Request $request)
    {
        $lesson_id = $request->input('lesson_id');
        $video_type = $request->input('video_type');

        if($video_type == 'vimeo')
        {
            $video_url = $this->parseVimeoURL($request->input('video_url'));
        }

        if($video_type == 'youtube')
        {
            $video_url = $this->parseYouTubeURL($request->input('video_url'));
        }

        if($video_type == 'drive')
        {
            $video_url = $request->input('video_url');
        }

        IETLSCourseLesson::where('id', $lesson_id)->update([
            'video_type' => $video_type,
            'video_url' => $video_url,
        ]);
        
        echo '<b class="text-success">New Video has been updated</b>';

        $this->reloadPage();
    }

    public function lockOrUnlockLesson(Request $request)
    {
        $lesson_id = $request->input('data')['lesson_id'];
        $isLocked = $request->input('data')['isLocked'];
        
        IETLSCourseLesson::where('id', $lesson_id)->update([
            'isLocked' => $isLocked == 'true' ? 1 : 0, 
        ]);

        $this->successMsg($isLocked == 'true' ? 'This lessson is locked' : 'This lessson is un-lockeded');
    }

    public function deleteLesson(Request $request)
    {
        $lesson_id = $request->input('lesson_id');

        if(IETLSCourseLesson::where('id', $lesson_id)->delete())
        {
            $this->reloadPage();
        }
    }

    public function previewMediaType(Request $request)
    {
        $media_type = $request->input('data')['media_type'];

        switch($media_type)
        {
            case 'image':
                return view('pages.courses.media-intro-type.image');
            break;
                
            case 'video':
                return view('pages.courses.media-intro-type.video');
            break;

            case 'none':
                return '';
            break;
        }
    }

    public function previewCoursePriceOption(Request $request)
    {
        $price_option = $request->input('data')['price_option'];

        if(isset($request->input('data')['ietls_course_id'])) 
        {
            $ietls_course_id = $request->input('data')['ietls_course_id'];
            
            $course = IETLSCourse::where('id', $ietls_course_id)->first();
        }
        else
        {
            $course = null;
        }

        switch($price_option)
        {
            case 'price':
                return view('pages.ietls-courses.price-options.price')->with('course', $course);
            break;
                
            case 'discount':
                return view('pages.ietls-courses.price-options.price-discount')->with('course', $course);
            break;

            case 'coupon':
                return view('pages.ietls-courses.price-options.coupon')->with('course', $course);
            break;

            case 'free':
                return '';
            break;
        }
    }
}
