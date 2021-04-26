<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReclameAquiCallbackController extends Controller
{
    public function ReclameAquiSession()
    {
        return response()->json([], 204);
    }

    public function ReclameAquiMessageCallback(Request $request)
    {
        sendMessageReclameAqui($request);

        $insert_params_messages = [
            'id'            => $request->correlationId,
            'type'          => 'reclame_aqui',
            'from_id'       => $request->correlationId,
            'to_id'         => $request->externalId,
            "created_at"    => Carbon::now()
        ];

        DB::table('messages')->insert($insert_params_messages);

        DB::table('reclame_aqui')->where('ticket_id', '=', $request['externalId'])->update(['reply' => '1',"updated_at" => Carbon::now()]);

        $acknowledgeParams = [
            'messages' => [
                [
                    'type' => 'DELIVERED',
                    'messageId' => $request->messageId
                ]
            ]
        ];

        sendFivenine($request->externalId, '', 'reclame_aqui', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        return response()->json(['success' => true, 'data' => 'Menssagem Respondida pelo Agente!'], 200);
    }

    public function ReclameAquiTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1',"updated_at" => Carbon::now()]);

        return response()->json([], 204);
    }

    public function ReclameAquiTyping(Request $request)
    {
        return response()->json([], 204);
    }

    public function ReclameAquiAccept(Request $request)
    {
        return response()->json([], 204);
    }
}
