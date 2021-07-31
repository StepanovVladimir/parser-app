<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $colleges = College::all();
        return view('welcome', ['colleges' => $colleges]);
    }
}
