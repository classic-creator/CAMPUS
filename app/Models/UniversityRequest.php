<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversityRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'collegeName',
        'address',
        'city',
        'email',
        'rating',
        'description',
        'create-by',
       
    ];
}
