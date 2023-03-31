<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'college1',
        'college2',
        'college3',
        'course1',
        'course2',
        'course3',
        'depertment1',
        'depertment2',
        'depertment3',
        'address',
    ];
}
