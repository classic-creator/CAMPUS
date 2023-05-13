<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
      
        'fees_id',
        'student_id',
        'payment_id',
        'payment_status',
        
        

    ];
}
