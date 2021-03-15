<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
            'id'            => $request->correlationId,
            'type'          => 'whatsapp',
            'from_id'       => $request->correlationId,
            'to_id'         => $request->externalId,
            "created_at"    => Carbon::now()
        ];

        DB::table('messages')->insert($insert_params_messages);

        $acknowledgeParams = [
            'messages' => [
                [
                    'type' => 'DELIVERED',
                    'messageId' => $request->messageId
                ]
            ]
        ];

        sendFivenine($request->externalId, '', 'whatsapp', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        return response()->json(['success' => true, 'data' => 'Menssagem Respondida pelo Agente!'], 200);
    }

    public function whatsappTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1',"updated_at" => Carbon::now()]);

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

            $quick_reply = false;
            $sender_email = false;
            $sender_name = false;
            $send_five9 = false;

            $sender_phone = $request->contactId;

            $text = $request->message;

            $contact_name = str_replace($request->contactId, '', $request->fromName);

            $config = DB::table('setting')->where('channel', '=', 'whatsapp')->first();

            $whatsapp_session = DB::table('whatsapp_conversations')->where('sender_phone', '=', $sender_phone)->orderBy('id', 'desc')->first();

            $bot_interations = DB::table('whatsapp_bots')->get();

            $interations_verify = DB::table('whatsapp_bots')->where('choice', '=', $text)->first();

            $billing_sessions = DB::table('billings')->where('network', '=', 'whatsapp')->first(['sessions']);

            $count_sessions = DB::table('conversation_sessions')->where('terminate', '=', 0)->where('channel', '=', 'whatsapp')->count();

            if (!is_null($billing_sessions->sessions) && $count_sessions >= $billing_sessions->sessions) {

                $request = (object) [
                    'externalId' => $sender_phone,
                    "text" => 'No momento, todos os nossos agentes estão ocupados, por favor retorne o seu contato mais tarde.',
                ];

                sendMessageWhatsapp($request);
            } else {
                if (!is_null($interations_verify)) {
                    $bot_variable = $interations_verify->variable;
                    $bot_choice = $interations_verify->value;
                    $send_five9 = true;
                }

                if (isset($whatsapp_session->conversationId)) {

                    $verify_session = DB::table('conversation_sessions')->where('conversationId', $whatsapp_session->conversationId)->where('terminate', '=', '0')->first();

                    if (!$verify_session) {
                        /* Create Session */

                        if (!$send_five9) {
                            if (isset($bot_interations) && $bot_interations) {

                                $message = 'Em qual Central deseja iniciar o Atendimento ?';

                                $message .= "
";

                                foreach ($bot_interations as $options) {
                                    $message .= "
";
                                    $message .= '*' . $options->choice . '-* ' . $options->options;
                                }

                                $request = (object) [
                                    'externalId' => $sender_phone,
                                    'text' => $message
                                ];

                                sendMessageWhatsapp($request);
                            }
                        }

                        if ($send_five9) {
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

                                if (isset($contact_name) && strtolower($contact_name) == "cadu leite") {
                                    $contact_name = "Carlos Eduardo Leite";
                                    $sender_email = "ceduardo@nuveto.com.br";
                                } else if (isset($request->contactId) && $request->contactId == "5511991239261") {
                                    $contact_name = "Andre Romeiro";
                                    $sender_email = "alromeiro@nuveto.com.br";
                                }

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
                                        'number1' => isset($request->contactId) ? '+' . $request->contactId : '+5511999999999',
                                        'email' => isset($sender_email) && $sender_email ? $sender_email : 'noreply@whatsapp.com.br',
                                    ],
                                    'externalId' => $sender_phone,
                                    'disableAutoClose' => true,
                                    'tenantId' => $create_session['orgId'],
                                    'type'  => 'WHATSAPP'
                                ];

                                if ($bot_variable && $bot_choice) {
                                    $params['attributes'] = [
                                        'Custom.' . $bot_variable => $bot_choice
                                    ];
                                }

                                $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                                if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                    $insert_params_conversation = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'userId'            => $sender_phone,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'tenantId'          => $create_session['orgId'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'channel'           => 'whatsapp',
                                        "created_at"        =>  Carbon::now()
                                    ];

                                    $insert_params_whatsapp = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'sender_phone'      => $sender_phone,
                                        'text'              => $text,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'payload'           => $request,
                                        "created_at"        =>  Carbon::now()
                                    ];

                                    DB::table('conversation_sessions')->insert($insert_params_conversation);
                                    DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                                }
                            }
                        }
                    } else {
                        $insert_params_whatsapp = [
                            'tokenId'           => $verify_session->tokenId,
                            'sender_phone'      => $sender_phone,
                            'text'              => $text,
                            'conversationId'    => $whatsapp_session->conversationId,
                            'farmId'            => $verify_session->farmId,
                            'payload'           => $request,
                            "created_at"        => Carbon::now()
                        ];

                        DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                    }
                } else {

                    /* Create Session */

                    if (!$send_five9) {
                        if (isset($bot_interations) && $bot_interations) {

                            $message = 'Em qual Central deseja iniciar o Atendimento?';

                            $message .= "
";

                            foreach ($bot_interations as $options) {
                                $message .= "
";
                                $message .= '*' . $options->choice . '-* ' . $options->options;
                            }

                            $request = (object) [
                                'externalId' => $sender_phone,
                                'text' => $message
                            ];

                            sendMessageWhatsapp($request);
                        }
                    }

                    if ($send_five9) {
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

                            if (isset($contact_name) && strtolower($contact_name) == "cadu leite") {
                                $contact_name = "Carlos Eduardo Leite";
                                $sender_email = "ceduardo@nuveto.com.br";
                            } else if (isset($request->contactId) && $request->contactId == "5511991239261") {
                                $contact_name = "Andre Romeiro";
                                $sender_email = "alromeiro@nuveto.com.br";
                            }

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
                                    'number1' => isset($request->contactId) ? '+' . $request->contactId : '+5511999999999',
                                    'email' => isset($sender_email) && $sender_email ? $sender_email : 'noreply@whatsapp.com.br',
                                ],
                                'externalId' => $sender_phone,
                                'disableAutoClose' => true,
                                'tenantId' => $create_session['orgId'],
                                'type'  => 'WHATSAPP'
                            ];

                            if ($bot_variable && $bot_choice) {
                                $params['attributes'] = [
                                    'Custom.' . $bot_variable => $bot_choice
                                ];
                            }

                            $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                            if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                $insert_params_conversation = [
                                    'tokenId'           => $create_session['tokenId'],
                                    'userId'            => $sender_phone,
                                    'conversationId'    => $create_conversation['body']['id'],
                                    'tenantId'          => $create_session['orgId'],
                                    'farmId'            => $create_session['context']['farmId'],
                                    'channel'           => 'whatsapp',
                                    "created_at"        =>  Carbon::now()
                                ];

                                $insert_params_whatsapp = [
                                    'tokenId'           => $create_session['tokenId'],
                                    'sender_phone'      => $sender_phone,
                                    'text'              => $text,
                                    'conversationId'    => $create_conversation['body']['id'],
                                    'farmId'            => $create_session['context']['farmId'],
                                    'payload'           => $request,
                                    "created_at"        =>  Carbon::now()
                                ];

                                DB::table('conversation_sessions')->insert($insert_params_conversation);
                                DB::table('whatsapp_conversations')->insert($insert_params_whatsapp);
                            }
                        }
                    }
                }

                if ($send_five9) {

                    sendFivenine($sender_phone, '', 'whatsapp');
                }
            }
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }

    public function whatsappStatus(Request $request)
    {
        Log::error($request);
        return response()->json(['success' => true, 'data' => 'Status Atualizado com Sucesso'], 200);
    }
}
