<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;

    protected $fillable = [
        'courseName',
        'college_id',
        'fees',
        'duration',
        'eligibility',
      
       
    ];
}
