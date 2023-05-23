<?php

namespace App\Http\Controllers\Api\Users\Students;

use App\Exceptions\Authorization\UnauthorizedException;
use App\Exceptions\Models\NotFoundException;
use App\Exceptions\Validation\DataValidationException;
use App\Http\Controllers\Controller;
use App\Models\IETLSCourses\IeltsUser;
use App\Models\Students\Student;
use App\Models\ZoomCourses\ZoomCourseUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

abstract class StudentController extends Controller {
    
    protected string $modelClassName;
    
    protected array $abilities = [
        'index'             => false,
        'store'             => false,
        'show'              => false,
        'update'            => false,
        'destroy'           => false,
        'updateProfilePic'  => false,
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Authorization\UnauthorizedException
     */
    public function index()
    {
        if (! $this->abilities['index'])
            throw new UnauthorizedException();

        return response()->json([
            'success' => true,
            'students' => call_user_func([$this->modelClassName, 'orderBy'], 'created_at', 'DESC')->get(['id', 'name', 'email', 'phone', 'image'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Authorization\UnauthorizedException
     * @throws \App\Exceptions\Validation\DataValidationException
     */
    public function store(Request $request)
    {
        if (! $this->abilities['store'])
            throw new UnauthorizedException();

        $tableName = app($this->modelClassName)->getTable();

        // validate request data
        $validator = Validator::make($request->all(), [
            'f_name'        => 'required|regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'l_name'        => 'required|regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'email'         => 'required|email|max:50|unique:' . $tableName . ',email',
            'password'      => 'required|min:8|max:80|string',
            'gender'        => 'required|in:male,female',
            'phone'         => 'regex:/^[0-9]{7,16}$/|unique:' . $tableName . ',phone',
        ]);

        if ($validator->fails())
            throw new DataValidationException($validator->errors()->all());

        // create new recorded course student
        $student = new $this->modelClassName;
        $student->updateName($request->f_name, $request->l_name); // update name property
        $student->email = $request->email;
        $student->password = Hash::make($request->password);
        $student->gender = $request->gender;
        $student->phone = $request->phone;
        $student->save(); // save student

        return response()->json([
            'success' => true,
            'student' => $student
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Authorization\UnauthorizedException
     */
    public function show($id)
    {
        if (! $this->abilities['show'])
            throw new UnauthorizedException();

            return response()->json([
                'success' => true,
                'student' => call_user_func(
                        [$this->modelClassName, 'with'],
                        ['courses' => function (BelongsToMany $query) {
                            $query->orderBy('created_at', 'DESC');
                        }]
                    )->find($id)
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Authorization\UnauthorizedException
     * @throws \App\Exceptions\Models\NotFoundException
     * @throws \App\Exceptions\Validation\DataValidationException
     */
    public function update(Request $request, $id)
    {
        if (! $this->abilities['update'])
            throw new UnauthorizedException();

        // search for student and throw Models\NotfoundException if student not found.
        $student = call_user_func([$this->modelClassName, 'find'], $id);
        if (! $student)
            throw new NotFoundException(Student::class, $id);

        $tableName = app($this->modelClassName)->getTable();

        // validate request data
        $validator = Validator::make($request->all(), [
            'f_name'        => 'regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'l_name'        => 'regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'email'         => 'email|max:50|unique:' . $tableName . ',email',
            'password'      => 'min:8|max:80|string',
            'gender'        => 'in:male,female',
            'phone'         => 'regex:/^[0-9]{7,16}$/|unique:' . $tableName . ',phone',
        ]);

        if ($validator->fails())
            throw new DataValidationException($validator->errors()->all());

        // update student data
        $student->updateName($request->f_name, $request->l_name);
        $student->email = $request->email ?? $student->email;
        $student->password = $request->password ? Hash::make($request->password) : $student->password;
        $student->gender = $request->gender ?? $student->gender;
        $student->phone = $request->phone ?? $student->phone;
        $student->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $this->abilities['destroy'])
            throw new UnauthorizedException();

        call_user_func([$this->modelClassName, 'where'], 'id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * update profile picture
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\Authorization\UnauthorizedException
     * @throws \App\Exceptions\Models\NotFoundException
     * @throws \App\Exceptions\Validation\DataValidationException
     */
    public function updateProfilePic(Request $request, $id)
    {
        if (! $this->abilities['updateProfilePic'])
            throw new UnauthorizedException();

        // search for student and throw Models\NotfoundException if student not found.
        $student = call_user_func([$this->modelClassName, 'find'], $id);
        if (! $student)
            throw new NotFoundException(Student::class, $id);

        // validate pic file
        $validator = Validator::make($request->all(), [
            'image' => 'file|mimes:jpg,jpeg,png,svg'
        ]);

        if ($validator->fails())
            throw new DataValidationException($validator->errors()->all());

        $studentDirName = "";
        if ($student instanceof Student)
            $studentDirName = "students";
        if ($student instanceof ZoomCourseUser)
            $studentDirName = "zoom-course-users";
        if ($student instanceof IeltsUser)
            $studentDirName = "ielts-course-users";

        // path to stored image
        $imagePath = $this->getUniversalPath('public/images/' . $studentDirName . '/' .$student->id);
        
        if ($request->has('image')) { // update image if request has file
            // file
            $file = $request->file('image');
            // store image to website public directory
            $file->move($imagePath , 'student.' . $file->getClientOriginalExtension()); // store image
            // save image name in student table
            $student->image = 'student.' . $file->getClientOriginalExtension();
            $student->save();
        } else if ($student->image) { // delete image if exists && request does not has file
            File::delete($imagePath . DIRECTORY_SEPARATOR . $student->image);
            $student->image = null;
            $student->save();
        }

        return response()->json(['success' => true]);
    }
}