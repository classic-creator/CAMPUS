<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'college_preference_1',
        'college_preference_2',
        'college_preference_3',
        'course_preference_1',
        'course_preference_2',
        'course_preference_3',
        'depertment_preference_1',
        'depertment_preference_2',
        'depertment_preference_3',
        'address_preference_1',
        'address_preference_2',
        'address_preference_3',
         
    ];
}
