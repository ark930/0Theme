<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class MainController extends Controller
{
    public function showPlan()
    {
        return view('plan');
    }

    public function setPlan()
    {
        return redirect('/plan/info');
    }

    public function showPlanInfo()
    {
        return 'showPlanInfo';
    }
}
