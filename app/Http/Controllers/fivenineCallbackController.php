<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class fivenineCallbackController extends Controller
{
    public function messageCreateCallback(Request $request)
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

        return response()->json(['data' => $request]);
    }

    public function messageCallback(Request $request)
    {
        $this->send($request);

        return response()->json(['data' => $request]);
    }

    public function terminateCallback(Request $request)
    {

        // DB::table('users')->where('id', $request['correlationId'])->delete();

        return response()->json(['ok' => 'ok']);
    }

    public function send($data)
    {
        // default variables
        $error_msg = $attachment = $attachment_title = null;

        $from_id = DB::table('users')->where('conversation_id', '=', $data['correlationId'])->first();

        if (!$error_msg) {
            // send to database
            $messageID = $data['correlationId'];
            Chatify::newMessage([
                'id' => (string) $messageID,
                'type' => 'API',
                'from_id' => (string) $from_id->id,
                'to_id' => (string) $data['externalId'],
                'body' => $data['text'],
                'attachment' => '',
            ]);

            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error_msg ? 1 : 0,
            'error_msg' => $error_msg,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $data['messageId'],
        ]);
    }
}
