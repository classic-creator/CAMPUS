<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Universitys extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'collegeName',
        'address',
        'email',
        'rating',
        'description',
        'create-by',
       
    ];

  

}
