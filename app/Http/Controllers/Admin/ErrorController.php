<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
	public function error403()
	{
		return view('pages.admin.errors.403');
	}
}
