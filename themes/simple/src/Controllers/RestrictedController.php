<?php

namespace Themes\SodaTheme\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RestrictedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('soda_theme_hint::logged_in', compact('request'));
    }
}