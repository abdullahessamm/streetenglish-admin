<?php

namespace App\Models\Exercises;

use ExerciseAnswerExerciseQuestion;
use App\Models\Exercises\ExerciseUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseAnswer extends Model
{
    protected $fillable = [
        'excercise_user_id', 'excercise_question_id', 'my_answer', 'correct_answer', 'question_score', 'user_score', 'isAnswerCorrect',
    ];

    public function belongsToExerciseQuestion()
    {
        return $this->belongsTo(ExerciseQuestion::class, 'excercise_question_id', 'id');
    }

    public function belongsToExerciseUser()
    {
        return $this->belongsTo(ExerciseUser::class, 'excercise_user_id', 'id');
    }
}
