<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageSubjectController extends Controller
{
    //
    public function index(){
        return view("admin.manage_subject");
    }
}
