<?php

namespace App\Http\Controllers\ProgramChair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageApprovalController extends Controller
{
    //
      public function index()
    {
        // 
        return view ('program-chair.manage-approval.index');
    }


    public function show()
    { 
       return view ('program-chair.manage-approval.show'); 
    }

}
