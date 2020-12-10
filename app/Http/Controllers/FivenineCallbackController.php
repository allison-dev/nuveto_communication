<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class fivenineCallbackController extends Controller
{
    public function chatSession(Request $request)
    {
        $faker = Faker::create();

        $insert_params = [
            'name'              => $faker->name(),
            'email'             => $faker->unique()->email(),
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
        // default variables
        $error_msg = null;

        $from_id = DB::table('users')->where('conversation_id', '=', $request['correlationId'])->first();

        if (!$error_msg) {
            // send to database
            $messageID = $request['correlationId'];
            Chatify::newMessage([
                'id' => (string) $messageID,
                'type' => 'API',
                'from_id' => (string) $from_id->id,
                'to_id' => (string) $request['externalId'],
                'body' => $request['text'],
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
            'tempID' => $request['messageId'],
        ]);
    }

    public function chatTerminate(Request $request)
    {
        DB::table('users')->where('conversation_id', $request['correlationId'])->delete();
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

        return response()->json([], 204);
    }

    public function chatAccept(Request $request)
    {
        DB::table('users')->where('conversation_id', $request['correlationId'])->update(['name' => $request['displayName']]);

        return response()->json([], 204);
    }

    public function chatTyping(Request $request)
    {
        return response()->json([], 204);
    }
}
