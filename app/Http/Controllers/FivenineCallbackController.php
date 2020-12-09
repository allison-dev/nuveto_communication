<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class fivenineCallbackController extends Controller
{
    public function chatSession(Request $request)
    {
        $insert_params = [
            'name'              => 'teste API',
            'email'             => 'teste@tes.com',
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'conversation_id'   => $request['correlationId'],
            'remember_token'    => Str::random(10),
        ];

        DB::table('users')->insert($insert_params);

        return response()->json([], 204);
    }

    public function chatCallback(Request $request)
    {
        sendChatCallback($request);

        return response()->json(['data' => $request]);
    }

    public function chatTerminate(Request $request)
    {
        // DB::table('users')->where('id', $request['correlationId'])->delete();

        return response()->json([], 204);
    }

    public function chatTyping(Request $request)
    {
        return response()->json([], 204);
    }
}
