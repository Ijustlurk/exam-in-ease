<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

use Auth;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {

        if (Auth::user()->roles[0]->name == "admin") {
            return view('layouts.Admin.app');
            
        } elseif (Auth::user()->roles[0]->name == "instructor") {

            return view('layouts.Instructor.app');

        } else {
            return view('layouts.ProgramChair.app');
        }
    }
}
