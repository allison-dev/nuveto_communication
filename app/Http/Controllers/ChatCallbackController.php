<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatCallbackController extends Controller
{
    public function chatSession()
    {
        return response()->json([], 204);
    }

    public function chatMessageCallback(Request $request)
    {
        sendMessageChat($request);

        $insert_params_messages = [
            'id'            => $request->correlationId,
            'type'          => 'chat',
            'from_id'       => $request->correlationId,
            'to_id'         => $request->externalId,
            "created_at"    =>  Carbon::now()
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

        sendFivenine($request->externalId, '', 'chat', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        return response()->json(['success' => true, 'data' => 'Menssagem Respondida pelo Agente!'], 200);
    }

    public function chatTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1', "updated_at" => Carbon::now()]);

        return response()->json([], 204);
    }

    public function chatTyping(Request $request)
    {
        return response()->json([], 204);
    }

    public function chatAccept(Request $request)
    {
        return response()->json([], 204);
    }

    public function chatCallback(Request $request)
    {
        $send_five9 = false;

        if (isset($request->message)) {
            if (isset($request->clientId) && isset($request->message) && isset($request->name) && isset($request->email)) {

                $send_five9 = true;

                $clientId = $request->clientId;

                $text = $request->message;

                $firstname = $request->name;

                $email = $request->email;

                $config = DB::table('setting')->where('channel', '=', 'chat')->first();

                $chat_session = DB::table('chat_conversations')->where('clientId', '=', $clientId)->orderBy('id', 'desc')->first();

                $billing_sessions = DB::table('billings')->where('network', '=', 'chat')->first(['sessions']);

                $count_sessions = DB::table('conversation_sessions')->where('terminate', '=', 0)->where('channel', '=', 'chat')->count();

                if (!is_null($billing_sessions->sessions) && $count_sessions >= $billing_sessions->sessions) {
                    $chat_req = (object) [
                        "text" => 'No momento, todos os nossos agentes estão ocupados, por favor retorne o seu contato mais tarde.',
                        "externalId" => $clientId,
                        "Name"  => "Sigma"
                    ];

                    sendMessageChat($chat_req);
                } else {

                    if (isset($chat_session->conversationId)) {

                        $verify_session = DB::table('conversation_sessions')->where('conversationId', $chat_session->conversationId)->where('terminate', '=', '0')->first();

                        if (!$verify_session) {
                            /* Create Session */

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

                                    if (isset($firstname) && strtolower($firstname) == "cadu leite") {
                                        $firstname = "Carlos Eduardo Leite";
                                        $email = "ceduardo@nuveto.com.br";
                                    } else if (isset($email) && strtolower($email) == "alromeiro@hotmail.com") {
                                        $firstname = "Andre Romeiro";
                                        $email = "alromeiro@nuveto.com.br";
                                    } else if (isset($email) && strtolower($email) == "fcontadini@hotmail.com") {
                                        $firstname = "Flamarion Contadini";
                                        $email = "fcontadini@nuveto.com.br";
                                    }

                                    $header = [
                                        'Content-Type'  => 'application/json',
                                        'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                        'farmId'        => $create_session['context']['farmId']
                                    ];

                                    $endpoint = 'conversations';

                                    $params = [
                                        'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/chat',
                                        'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                        'contact' => [
                                            'email' => isset($email) ? $email : 'noreply_' . $clientId . '@chat.com',
                                            'firstName' => isset($firstname) ? $firstname : 'Sigma Chat User',
                                        ],
                                        'externalId' => $clientId,
                                        'disableAutoClose' => true,
                                        'tenantId' => $create_session['orgId'],
                                        'type'  => 'chat'
                                    ];

                                    $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                                    if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                        $insert_params_conversation = [
                                            'tokenId'           => $create_session['tokenId'],
                                            'userId'            => $clientId,
                                            'conversationId'    => $create_conversation['body']['id'],
                                            'tenantId'          => $create_session['orgId'],
                                            'farmId'            => $create_session['context']['farmId'],
                                            'channel'           => 'chat',
                                            "created_at"        =>  Carbon::now()
                                        ];

                                        $insert_params_chat = [
                                            'tokenId'           => $create_session['tokenId'],
                                            'clientId'         => $clientId,
                                            'text'              => $text,
                                            'conversationId'    => $create_conversation['body']['id'],
                                            'farmId'            => $create_session['context']['farmId'],
                                            'farmId'            => $create_session['context']['farmId'],
                                            'payload'           => $request,
                                            "created_at"        =>  Carbon::now()
                                        ];

                                        if (isset($request->hasAttachment) && $request->hasAttachment) {
                                            $insert_params_chat_medias = [
                                                'conversationId'    => $create_conversation['body']['id'],
                                                'channel'           => "chat",
                                                "type"              => "s3",
                                                "created_at"        =>  Carbon::now()
                                            ];
                                            if ($request->image) {
                                                $image_text = 'Imagem enviada em Anexo!';
                                                $image_text .= "
";
                                                $insert_params_chat_medias['image'] = $request->image;
                                                $insert_params_chat['text'] = html_entity_decode($image_text . $text);
                                            }

                                            DB::table('medias')->insert($insert_params_chat_medias);
                                        }

                                        DB::table('conversation_sessions')->insert($insert_params_conversation);
                                        DB::table('chat_conversations')->insert($insert_params_chat);
                                    }
                                }
                            }
                        } else {
                            $insert_params_chat = [
                                'tokenId'           => $verify_session->tokenId,
                                'clientId'         => $clientId,
                                'text'              => $text,
                                'conversationId'    => $chat_session->conversationId,
                                'farmId'            => $verify_session->farmId,
                                'payload'           => $request,
                                "created_at"        =>  Carbon::now()
                            ];

                            if (isset($request->hasAttachment) && $request->hasAttachment) {
                                $insert_params_chat_medias = [
                                    'conversationId'    => $chat_session->conversationId,
                                    'channel'           => "chat",
                                    "type"              => "s3",
                                    "created_at"        =>  Carbon::now()
                                ];
                                if ($request->image) {
                                    $image_text = 'Imagem enviada em Anexo!';
                                    $image_text .= "
";
                                    $insert_params_chat_medias['image'] = $request->image;
                                    $insert_params_chat['text'] = html_entity_decode($image_text . $text);
                                }

                                DB::table('medias')->insert($insert_params_chat_medias);
                            }

                            DB::table('chat_conversations')->insert($insert_params_chat);
                        }
                    } else {
                        /* Create Session */
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

                                if (isset($firstname) && strtolower($firstname) == "cadu leite") {
                                    $firstname = "Carlos Eduardo Leite";
                                    $email = "ceduardo@nuveto.com.br";
                                } else if (isset($email) && strtolower($email) == "alromeiro@hotmail.com") {
                                    $firstname = "Andre Romeiro";
                                    $email = "alromeiro@nuveto.com.br";
                                } else if (isset($email) && strtolower($email) == "fcontadini@hotmail.com") {
                                    $firstname = "Flamarion Contadini";
                                    $email = "fcontadini@nuveto.com.br";
                                }

                                $header = [
                                    'Content-Type'  => 'application/json',
                                    'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                    'farmId'        => $create_session['context']['farmId']
                                ];

                                $endpoint = 'conversations';

                                $params = [
                                    'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/chat',
                                    'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                    'contact' => [
                                        'email' => isset($email) ? $email : 'noreply_' . $clientId . '@chat.com',
                                        'firstName' => isset($firstname) ? $firstname : 'Sigma Chat User',
                                    ],
                                    'externalId' => $clientId,
                                    'disableAutoClose' => true,
                                    'tenantId' => $create_session['orgId'],
                                    'type'  => 'chat'
                                ];

                                $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                                if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                    $insert_params_conversation = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'userId'            => $clientId,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'tenantId'          => $create_session['orgId'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'channel'           => 'chat',
                                        "created_at"        =>  Carbon::now()
                                    ];

                                    $insert_params_chat = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'clientId'         => $clientId,
                                        'text'              => $text,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'payload'           => $request,
                                        "created_at"        =>  Carbon::now()
                                    ];

                                    if (isset($request->hasAttachment) && $request->hasAttachment) {
                                        $insert_params_chat_medias = [
                                            'conversationId'    => $create_conversation['body']['id'],
                                            'channel'           => "chat",
                                            "type"              => "s3",
                                            "created_at"        =>  Carbon::now()
                                        ];

                                        if ($request->image) {
                                            $image_text = 'Imagem enviada em Anexo!';
                                            $image_text .= "
";
                                            $insert_params_chat_medias['image'] = $request->image;
                                            $insert_params_chat['text'] = html_entity_decode($image_text . $text);
                                        }

                                        DB::table('medias')->insert($insert_params_chat_medias);
                                    }

                                    DB::table('conversation_sessions')->insert($insert_params_conversation);
                                    DB::table('chat_conversations')->insert($insert_params_chat);
                                }
                            }
                        }
                    }

                    if ($send_five9) {
                        sendFivenine($clientId, '', 'chat');
                    }
                }
            }
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }
}
