<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
          'user_id',
          'state',
          'district',
          'sub_district',
          'circle_office',
          'pin_no',
          'police_station',
          'post_office',

        ];
}
