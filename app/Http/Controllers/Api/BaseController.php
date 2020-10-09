<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
	public function responseSuccess($result, $code = 200)
	{
		return response()->json($result, $code);
	}

	public function responseError($error, $code = 404)
	{
		return response()->json($error, $code);
	}
}
