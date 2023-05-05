<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class collegeImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_name',
        'image_path',
        'college_id',
        'type',  
    ];
}
