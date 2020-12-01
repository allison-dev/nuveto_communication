<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class fivenineCallbackController extends Controller
{
    public function messageCreateCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }

    public function messageCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }

    public function terminateCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }
}
