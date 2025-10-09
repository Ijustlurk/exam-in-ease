<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamStatisticsController extends Controller
{
    //

       public function index()
    {
        // 
        return view ('program-chair.exam-statistics.index');
    }
}
