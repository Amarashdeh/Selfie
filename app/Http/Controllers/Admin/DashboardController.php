<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (!in_array(auth()->guard('admin')->user()->getRoleNames()->first(), ['SuperAdmin', 'Admin'])) {
            abort(403, 'Unauthorized (Admin only)');
        }
    }
    public function index()
    {
        return view('admin.dashboard');
    }
}
