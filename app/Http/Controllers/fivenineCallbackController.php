<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Response;

class fivenineCallbackController extends Controller
{
    public function messageCreateCallback(Request $request)
    {
        return response()->json(['data' => $request]);
    }

    public function messageCallback(Request $request)
    {
        $this->send($request);

        return response()->json(['data' => $request]);
    }

    public function terminateCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }

    public function send($data)
    {
        // default variables
        $error_msg = $attachment = $attachment_title = null;

        if (!$error_msg) {
            // send to database
            $messageID = $data['correlationId'];
            Chatify::newMessage([
                'id' => (string) $messageID,
                'type' => 'API',
                'from_id' => (string) $data['correlationId'],
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
