<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
       'student_id',
       'college_id',
       'course_id',
       'personalDetails_id',
       'educationalDetails_id',
       'address_id',
       'payment_status',
       'admission_status'
       
    ];
}
