<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEducationalDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        "class10_passingYear",
        "class10_roll",
        "class10_no",
        "class10_board",
        "class10_school",
        "class10_totalMark",
        "class10_markObtain",
        "class12_passingYear",
        "class12_roll",
        "class12_no",
        "class12_board",
        "class12_college",
        "class12_strem",
        "class12_totalMark",
        "class12_markObtain",
    ];
}
