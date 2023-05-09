@extends('layouts.app', [
    'title' => $exerciseUser->belongsToLiveCourseUser->name."'s Answers",
    'active' => 'survey-js.index',
    'breadcrumb' => [
        'title' => $exerciseUser->belongsToLiveCourseUser->name."'s Answers",
        'map' => [
            'Homepage' => 'home',
            'Zoom Courses' => 'zoom-courses',
            $zoomCourseLevelSession->belongsToZoomCourseLevel->belongsToZoomCourse->title => [
				'route' => 'zoom-course.show',
				'slug' => $zoomCourseLevelSession->belongsToZoomCourseLevel->belongsToZoomCourse->slug
			],
            $zoomCourseLevelSession->belongsToZoomCourseLevel->title => [
				'route' => 'zoom-course.level.show',
				'slug' => [
                    $zoomCourseLevelSession->belongsToZoomCourseLevel->belongsToZoomCourse->slug,
                    $zoomCourseLevelSession->belongsToZoomCourseLevel->slug,
                ]
			],
            $zoomCourseLevelSession->title => [
                'route' => 'zoom-course.level.session.show',
                'slug' => [
                    $zoomCourseLevelSession->belongsToZoomCourseLevel->belongsToZoomCourse->slug,
                    $zoomCourseLevelSession->belongsToZoomCourseLevel->slug,
                    $zoomCourseLevelSession->slug,
                ]
            ],
            $exerciseUser->belongsToLiveCourseUser->name."'s Answers" => 'active'
        ]
    ],
    'scripts' => 'pages.survey-js.user.show',
])


@section('content')
<!-- Create new Survey Form -->
<section class="content text-right">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ $exerciseUser->belongsToLiveCourseUser->name."'s Answers"  }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                @php
                    $x = 1;
                @endphp
                <div class="col-12 text-right" dir="ltr">
                    <form id="correct-answers">
                        {{ csrf_field() }}
                        <input type="hidden" name="exercise_user_id" value="{{ $exerciseUser->id }}">
                        @for($i = 0; $i < count($exercise_pages); $i++)
                            @for ($j = 0; $j < count($exercise_pages[$i]['elements']); $j++)
                                @php
                                $exerciseQuestion = App\Models\Exercises\ExerciseQuestion::where('exercise_id', $exercise->id)->where('question_name', $exercise_pages[$i]['elements'][$j]['name'])->first();
                                $exercise_question_id = \App\Models\Exercises\ExerciseQuestion::where('exercise_id', $exercise->id)->where('question_name', $exercise_pages[$i]['elements'][$j]['name'])->first() == null ? null : \App\Models\Exercises\ExerciseQuestion::where('exercise_id', $exercise->id)->where('question_name', $exercise_pages[$i]['elements'][$j]['name'])->first()->id;
                                $exerciseUserAnswer = \App\Models\Exercises\ExerciseAnswer::where('exercise_user_id', $exerciseUser->id)->where('exercise_question_id', $exercise_question_id)->first();
                                @endphp
                                
                                @if($exercise_pages[$i]['elements'][$j]['type'] == 'text')
                                <input type="hidden" name="user_answer_id[]" value="{{ $exerciseUserAnswer->id }}">
                                <h3>{{$x++}} - Question : {{ $exercise_pages[$i]['elements'][$j]['title'] }} <input type="number" name="user_score[]" value="{{ $exerciseUserAnswer->user_score }}" min="0" style="width: 50px;height: 30px;" required> / <input type="number" name="question_score[]" value="{{ $exerciseQuestion->score }}" min="0" style="width: 50px;height: 30px;" required></h3>
                                <h3 class="text-success">User's Answer</h3>
                                <h4>{{ $exerciseUserAnswer->my_answer }}</h4>

                                <div class="form-group">
                                    <label for="correct_answer_{{$exercise_question_id}}">
                                        The Correct Answer
                                        <small class="font-weight-bold text-danger">Optional</small>
                                    </label>
                                    <textarea name="correct_answer[]" class="form-control" id="correct_answer_{{$exercise_question_id}}" cols="30" rows="3"></textarea>
                                </div>
                        
                                @elseif($exercise_pages[$i]['elements'][$j]['type'] == 'comment')
                                <input type="hidden" name="user_answer_id[]" value="{{ $exerciseUserAnswer->id }}">
                                <h3>{{$x++}} - Question : {{ $exercise_pages[$i]['elements'][$j]['title'] }} <input type="number" name="user_score[]" value="{{ $exerciseUserAnswer->user_score }}" min="0" style="width: 50px;height: 30px;" required> / <input type="number" name="question_score[]" value="{{ $exerciseQuestion->score }}" min="0" style="width: 50px;height: 30px;" required></h3>
                                <h3 class="text-success">User's Answer</h3>
                                <h4>{{ $exerciseUserAnswer->my_answer }}</h4>
                                
                                <div class="form-group">
                                    <label for="correct_answer_{{$exercise_question_id}}">
                                        The Correct Answer
                                        <small class="font-weight-bold text-danger">Optional</small>
                                    </label>
                                    <textarea name="correct_answer[]" class="form-control" id="correct_answer_{{$exercise_question_id}}" cols="30" rows="3"></textarea>
                                </div>

                                @elseif($exercise_pages[$i]['elements'][$j]['type'] == 'checkbox')
                                <input type="hidden" name="user_answer_id[]" value="{{ $exerciseUserAnswer->id }}">
                                <h3>{{$x++}} - Question : {{ $exercise_pages[$i]['elements'][$j]['title'] }} <input type="number" name="user_score[]" value="{{ $exerciseUserAnswer->user_score }}" min="0" style="width: 50px;height: 30px;" required> / <input type="number" name="question_score[]" value="{{ $exerciseQuestion->score }}" min="0" style="width: 50px;height: 30px;" required></h3>
                                <h3 class="text-success">User's Answer</h3>
                                @php
                                    $unserialzed_answers = unserialize($exerciseUserAnswer->my_answer);
                                @endphp
                                
                                @foreach ($unserialzed_answers as $unserialzed_answer)
                                <h4>{{ $unserialzed_answer }}</h4>
                                @endforeach

                                <div class="form-group">
                                    <label for="correct_answer_{{$exercise_question_id}}">
                                        The Correct Answer
                                        <small class="font-weight-bold text-danger">Optional</small>
                                    </label>
                                    <textarea name="correct_answer[]" class="form-control" id="correct_answer_{{$exercise_question_id}}" cols="30" rows="3"></textarea>
                                </div>
    
                                @elseif($exercise_pages[$i]['elements'][$j]['type'] == 'html')
                                
                                {!! $exercise_pages[$i]['elements'][$j]['html'] !!}
    
                                @elseif($exercise_pages[$i]['elements'][$j]['type'] == 'radiogroup')
                                <input type="hidden" name="user_answer_id[]" value="{{ $exerciseUserAnswer->id }}">
                                <h3>{{$x++}} - Question : {{ $exercise_pages[$i]['elements'][$j]['title'] }} <input type="number" name="user_score[]" value="{{ $exerciseUserAnswer->user_score }}" min="0" style="width: 50px;height: 30px;" required> / <input type="number" name="question_score[]" value="{{ $exerciseQuestion->score }}" min="0" style="width: 50px;height: 30px;" required></h3>
                                <h3 class="text-success">User's Answer</h3>
                                <h4>{{ $exerciseUserAnswer->my_answer }}</h4>

                                <div class="form-group">
                                    <label for="correct_answer_{{$exercise_question_id}}">
                                        The Correct Answer
                                        <small class="font-weight-bold text-danger">Optional</small>
                                    </label>
                                    <textarea name="correct_answer[]" class="form-control" id="correct_answer_{{$exercise_question_id}}" cols="30" rows="3"></textarea>
                                </div>
    
                                @endif
                                <hr>
                            @endfor
                        @endfor
                        <div class="modal-footer">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                    {{-- @php
                        $x = 1;
                    @endphp
                    <form id="correct-answers">
                        {{ csrf_field() }}
                        <input type="hidden" name="exercise_user_id" value="{{ $exerciseUser->id }}">
                        @foreach ($exerciseUser->answers as $eachAnswer)
                            @if(!empty($eachAnswer->belongsToSurveyQuestion->question))
                                
                            <input type="hidden" name="user_answer_id[]" value="{{ $eachAnswer->id }}">
                            <div class="text-right" dir="ltr">
                                <h3>{{ $x++ }} - Question : {{ $eachAnswer->belongsToSurveyQuestion->question }} <input type="number" name="user_score[]" value="{{ $exerciseUserAnswer->user_score }}" min="0" style="width: 50px;height: 30px;" required> / <input type="number" name="question_score[]" value="{{ $exerciseQuestion->score }}" min="0" style="width: 50px;height: 30px;" value="{{ $eachAnswer->belongsToSurveyQuestion->score }}" required></h3>
                            </div>
                            
                            @if(@unserialize($eachAnswer->my_answer) )
                            <h5 class="text-right" dir="ltr">User Answers</h5>
                            @php
                                $unserialized_answers = unserialize($eachAnswer->my_answer);
                            @endphp
        
                            <ol class="text-right" dir="ltr">
                            @foreach($unserialized_answers as $unserialized_answer)
                                <li>- {{ $unserialized_answer }}</li>    
                            @endforeach
                            </ol>
                            @else
                            <h5 class="text-right" dir="ltr">User Answer : {{ $eachAnswer->my_answer }}</h5>
                            @endif
                            
                            <p>The Correct answers <small class="text-danger font-weight">Optional</small></p>
                            <textarea name="correct_answer[]" class="form-control" id="correct_answer" cols="30" rows="3"></textarea>
                            <hr>
                            @endif
                        @endforeach

                        <div class="modal-footer">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Loading Modal -->
<div class="modal" id="loading" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-right">
            <div class="modal-body">
                <div class="progress text-right">
                    <div id="progressbar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal" id="resModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-right">
            <div class="modal-body text-center">
                <div id="res"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="onCloseModal"> Close Window</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal" id="error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-right">
            <div class="modal-body text-center">
				<span class="fa fa-times text-danger" style="font-size: 100px;"></span>
				<h1>Error form the Server</h1>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="onCloseModal"> Close Window</button>
            </div>
        </div>
    </div>
</div>
@endsection