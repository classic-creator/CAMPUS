<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_seat',
        'OBC',
        'SC',
        'ST',
        'EWS',
        'other',
        'course_id',
        'open',

    ];
}
