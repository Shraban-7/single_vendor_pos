<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Base Admin Controller
 * 
 * All admin controllers should extend this class.
 */
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Add any common admin middleware here
        // $this->middleware('auth');
        // $this->middleware('admin');
    }
}
