<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //@desc Show all job listings
    //@route GET/login
    public function index(): View
    {
        $jobs = Job::latest()->limit(6)->get();
        return view("pages.index")->with("jobs", $jobs);
    }
}
