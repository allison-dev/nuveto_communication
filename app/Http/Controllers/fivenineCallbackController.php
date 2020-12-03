<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class fivenineCallbackController extends Controller
{
    public function messageCreateCallback(Request $request)
    {
        $data = json_decode($request, true);

        // $this->send($data);

        return response()->json(['data' => $data]);
    }

    public function messageCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }

    public function terminateCallback(Request $request)
    {

        return response()->json(['ok' => 'ok']);
    }

    // public function send($data)
    // {
    //     // default variables
    //     $error_msg = $attachment = $attachment_title = null;

    //     if (!$error_msg) {
    //         // send to database
    //         $messageID = $data['correlationId'];
    //         Chatify::newMessage([
    //             'id' => $messageID,
    //             'type' => 'API',
    //             'from_id' => Auth::user()->id,
    //             'to_id' => Auth::user()->id,
    //             'body' => trim(htmlentities($data['message'])),
    //             'attachment' => ($attachment) ? $attachment . ',' . $attachment_title : null,
    //         ]);

    //         // fetch message to send it with the response
    //         $messageData = Chatify::fetchMessage($messageID);

    //         // send to user using pusher
    //         Chatify::push('private-chatify', 'messaging', [
    //             'from_id' => Auth::user()->id,
    //             'to_id' => $data['id'],
    //             'message' => Chatify::messageCard($messageData, 'default')
    //         ]);
    //     }

    //     // send the response
    //     return Response::json([
    //         'status' => '200',
    //         'error' => $error_msg ? 1 : 0,
    //         'error_msg' => $error_msg,
    //         'message' => Chatify::messageCard(@$messageData),
    //         'tempID' => $request['temporaryMsgId'],
    //     ]);
    // }
}
