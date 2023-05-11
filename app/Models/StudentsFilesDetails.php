<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsFilesDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'profile_photo',
        'aadhar',
        'signature',
        'hslc_registation',
        'hslc_marksheet',
        'hslc_certificate',
        'hslc_admit',
        'hsslc_registation',
        'hsslc_marksheet',
        'hsslc_certificate',
        'hsslc_admit',
       
    ];

}
