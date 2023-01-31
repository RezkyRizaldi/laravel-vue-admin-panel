<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __invoke(Request $request): View|Factory
    {
        return view('admin.layouts.app');
    }
}
