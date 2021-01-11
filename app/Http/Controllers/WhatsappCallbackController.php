<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsappCallbackController extends Controller
{
    public function whatsappSession()
    {
        return response()->json([], 204);
    }

    public function whatsappMessageCallback(Request $request)
    {
        sendMessageWhatsapp($request);

        $insert_params_messages = [
            'id'        => $request->correlationId,
            'type'      => 'whatsapp',
            'from_id'   => $request->correlationId,
            'to_id'     => $request->externalId,
        ];

        DB::table('messages')->insert($insert_params_messages);

        $acknowledgeParams = [
            'messages' => [
                [
                    'type' => 'DELIVERED',
                    'messageId' => $request['messageId']
                ]
            ]
        ];

        $teste = sendFivenine($request->correlationId, '', 'chat', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        Log::error(json_encode($teste));
        Log::error(json_encode($acknowledgeParams));


        return response()->json(['success' => true, 'data' => 'Menssagem Respondida pelo Agente!'], 200);
    }

    public function whatsappTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

        return response()->json([], 204);
    }

    public function whatsappTyping(Request $request)
    {
        return response()->json([], 204);
    }

    public function whatsappAccept(Request $request)
    {
        return response()->json([], 204);
    }

    public function whatsappCallback(Request $request)
    {
        if (isset($request->WHATSAPP_NUMBER) && isset($request->message) && isset($request->fromCustomer) && $request->fromCustomer) {

            $sender_phone = $request->contactId;

            $text = $request->message;

            $contact_name = str_replace($request->contactId, '', $request->fromName);

            $config = DB::table('setting')->where('channel', '=', 'whatsapp')->first();

            $whatsapp_session = DB::table('whatsapp_conversations')->where('sender_phone', '=', $sender_phone)->orderBy('id', 'desc')->first();

            if (isset($whatsapp_session->conversationId)) {

                $verify_session = DB::table('conversation_sessions')->where('conversationId', $whatsapp_session->conversationId)->where('terminate', '=', '0')->first();

                if (!$verify_session) {
                    /* Create Session */

                    $header = [
                        'Accept'       => 'application/json',
                        'Content-Type' => 'application/json',
                    ];

                    $endpoint = 'auth/anon?cookieless=true';

                    $params = [
                        'tenantName' => isset($config->tenantName) && !empty($config->tenantName) ? $config->tenantName : 'nuveto'
                    ];

                    $create_session = apiCall($header, $endpoint, 'POST', $params);

                    if (isset($create_session['tokenId']) && $create_session['tokenId']) {

                        $header = [
                            'Content-Type'  => 'application/json',
                            'Authorization' => 'Bearer-' . $create_session['tokenId'],
                            'farmId'        => $create_session['context']['farmId']
                        ];

                        $endpoint = 'conversations';

                        $params = [
                            'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/whatsapp',
                            'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                            'contact' => [
                                'firstName' => isset($contact_name) ? trim($contact_name) : 'Whatsapp User',
                                'socialAccountName' => isset($request->fromName) ? $request->fromName : 'Whatsapp User',
                                'number1' => isset($request->contactId) ? $request->contactId : '+5511999999999',
                            ],
                            'externalId' => $sender_phone,
                            'disableAutoClose' => true,
                            'tenantId' => $create_session['orgId'],
                            'type'  => 'WHATSAPP'
                        ];

                        $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                        if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                            $insert_params_conversation = [
                                'tokenId'           => $create_session['tokenId'],
                                'userId'            => $sender_phone,
                                'conversationId'    => $create_conversation['body']['id'],
                                'tenantId'          => $create_session['orgId'],
                                'farmId'            => $create_session['context']['farmId']
                            ];

                            $insert_params_whatsapp = [
                                'tokenId'           => $create_session['tokenId'],
                                'sender_phone'      => $sender_phone,
                                'text'              => $text,
                                'conversationId'    => $create_conversation['body']['id'],
                                'farmId'            => $create_session['context']['farmId'],
                                'farmId'            => $create_session['context']['farmId'],
                                'payload'           => $request
                            ];

                            DB::table('conversation_sessions')->insert($insert_params_conversation);
                            DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                        }
                    }
                } else {
                    $insert_params_whatsapp = [
                        'tokenId'           => $verify_session->tokenId,
                        'sender_phone'      => $sender_phone,
                        'text'              => $text,
                        'conversationId'    => $whatsapp_session->conversationId,
                        'farmId'            => $verify_session->farmId,
                        'payload'           => $request
                    ];

                    DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                }
            } else {

                /* Create Session */

                $header = [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ];

                $endpoint = 'auth/anon?cookieless=true';

                $params = [
                    'tenantName' => isset($config->tenantName) && !empty($config->tenantName) ? $config->tenantName : 'nuveto'
                ];

                $create_session = apiCall($header, $endpoint, 'POST', $params);

                if (isset($create_session['tokenId']) && $create_session['tokenId']) {
                    $header = [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer-' . $create_session['tokenId'],
                        'farmId'        => $create_session['context']['farmId']
                    ];

                    $endpoint = 'conversations';

                    $params = [
                        'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/whatsapp',
                        'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                        'contact' => [
                            'firstName' => isset($contact_name) ? trim($contact_name) : 'Whatsapp User',
                            'socialAccountName' => isset($request->fromName) ? $request->fromName : 'Whatsapp User',
                            'number1' => isset($request->contactId) ? $request->contactId : '+5511999999999',
                        ],
                        'externalId' => $sender_phone,
                        'disableAutoClose' => true,
                        'tenantId' => $create_session['orgId'],
                        'type'  => 'WHATSAPP'
                    ];

                    $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                    if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                        $insert_params_conversation = [
                            'tokenId'           => $create_session['tokenId'],
                            'userId'            => $sender_phone,
                            'conversationId'    => $create_conversation['body']['id'],
                            'tenantId'          => $create_session['orgId'],
                            'farmId'            => $create_session['context']['farmId']
                        ];

                        $insert_params_whatsapp = [
                            'tokenId'           => $create_session['tokenId'],
                            'sender_phone'      => $sender_phone,
                            'text'              => $text,
                            'conversationId'    => $create_conversation['body']['id'],
                            'farmId'            => $create_session['context']['farmId'],
                            'payload'           => $request
                        ];

                        DB::table('conversation_sessions')->insert($insert_params_conversation);
                        DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                    }
                }
            }

            sendFivenine($sender_phone, '', 'whatsapp');
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }

    public function whatsappStatus(Request $request)
    {
        Log::error($request);
        return response()->json(['success' => true, 'data' => 'Status Atualizado com Sucesso'], 200);
    }
}
