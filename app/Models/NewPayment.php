<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'fees_type',
        'fees_id',
        'amount',
        'last_date',
        'course_id',
        'active_status',

    ];
}
