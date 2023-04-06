<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPersonalDetails extends Model
{
    use HasFactory;
    protected $table = 'student_personal_data';
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'father_name',
        'mother_name',
        'dob',
        'phon_no',
        'identification',
        'identification_no',
        'qualification',
        'mark_obtain_lastExam',
    ];
}
