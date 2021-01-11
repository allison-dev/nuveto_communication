<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Thujohn\Twitter\Facades\Twitter;

class twitterCallbackController extends Controller
{
    public function twitterSession()
    {
        return response()->json([], 204);
    }

    public function twitterMessageCallback(Request $request)
    {
        sendMessageTwitter($request);

        $insert_params_messages = [
            'id'        => $request->correlationId,
            'type'      => 'twitter',
            'from_id'   => $request->correlationId,
            'to_id'     => $request->externalId,
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

        sendFivenine($request->externalId, '', 'twitter', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        return response()->json([], 204);
    }

    public function twitterTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

        return response()->json([], 204);
    }

    public function twitterTyping(Request $request)
    {
        return response()->json([], 204);
    }

    public function twitterAccept(Request $request)
    {
        return response()->json([], 204);
    }

    public function twitterCallback(Request $request)
    {
        $quick_reply = false;
        $sender_email = false;
        $sender_name = false;

        if (isset($request->direct_message_events)) {
            $data = $request->direct_message_events;

            $events = $data[0];

            $credentials = Twitter::getCredentials([
                'include_email' => 'true',
                'include_entities' => 'false',
                'skip_status' => 'true'
            ]);

            if (isset($events['message_create']['sender_id']) && $events['message_create']['sender_id'] != $credentials->id_str) {

                $sender_id = $events['message_create']['sender_id'];

                $recipient_id = $events['message_create']['target']['recipient_id'];

                $text = $events['message_create']['message_data']['text'];

                $config = DB::table('setting')->where('channel', '=', 'twitter')->first();

                $twitter_session = DB::table('twitter_conversations')->where('sender_id', '=', $sender_id)->orderBy('id', 'desc')->first();

                $first_interation = DB::table('messages')->where('from_id', "=", $sender_id)->orderBy('id', 'desc')->first(['first_interation']);


                if (isset($events['message_create']['message_data']['quick_reply_response'])) {
                    $verify_twitter_email = true;
                    $sender_email = $text;
                } else {
                    $verify_twitter_email = false;
                }

                if (isset($twitter_session->conversationId)) {

                    $verify_session = DB::table('conversation_sessions')->where('conversationId', $twitter_session->conversationId)->where('terminate', '=', '0')->first();

                    if (!$verify_session) {

                        if ($verify_twitter_email) {
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

                                if (isset($request->users[$sender_id]['name']) && strtolower($request->users[$sender_id]['name']) == "cadu leite") {
                                    $sender_name = "Carlos Eduardo Leite";
                                    $sender_email = "ceduardo@nuveto.com.br";
                                } else if (isset($request->users[$sender_id]['screen_name']) && strtolower($request->users[$sender_id]['screen_name']) == "alromeiro") {
                                    $sender_name = "Andre Romeiro";
                                    $sender_email = "alromeiro@nuveto.com.br";
                                } else if (isset($request->users[$sender_id]['name']) && $request->users[$sender_id]['name']) {
                                    $sender_name = $request->users[$sender_id]['name'];
                                }

                                $header = [
                                    'Content-Type'  => 'application/json',
                                    'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                    'farmId'        => $create_session['context']['farmId']
                                ];

                                $endpoint = 'conversations';

                                $params = [
                                    'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/twitter',
                                    'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                    'contact' => [
                                        'email' => isset($sender_email) && $sender_email ? $sender_email : 'noreply_' . $sender_id . '@twitter.com',
                                        'firstName' => isset($sender_name) && $sender_name ? $sender_name : 'Twitter User'
                                    ],
                                    'externalId' => $sender_id,
                                    'disableAutoClose' => true,
                                    'tenantId' => $create_session['orgId'],
                                    'type'  => 'TWITTER'
                                ];

                                $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                                if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                    $insert_params_conversation = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'userId'            => $sender_id,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'tenantId'          => $create_session['orgId'],
                                        'farmId'            => $create_session['context']['farmId']
                                    ];

                                    $insert_params_twitter = [
                                        'tokenId'           => $create_session['tokenId'],
                                        'sender_id'         => $sender_id,
                                        'text'              => $text,
                                        'conversationId'    => $create_conversation['body']['id'],
                                        'farmId'            => $create_session['context']['farmId'],
                                        'payload'           => $request
                                    ];

                                    DB::table('conversation_sessions')->insert($insert_params_conversation);
                                    DB::table('twitter_conversations')->insert($insert_params_twitter);
                                }
                            }
                        } else {
                            if (!$first_interation) {
                                $twitter_req = [
                                    "text" => "Por questões de Segurança, Informe o seu e-mail para iniciar seu atendimento!",
                                    "externalId" => $sender_id,
                                    "to" => $recipient_id,
                                    "messageId" => $events['id']
                                ];

                                sendMessageTwitter($twitter_req, false, true);
                            } else {
                                $twitter_req = [
                                    "text" => "Confirme Abaixo o seu E-mail!",
                                    "email" => $text,
                                    "externalId" => $sender_id
                                ];

                                sendMessageTwitter($twitter_req, true, false);
                            }

                            $quick_reply = true;
                        }
                    } else {
                        $insert_params_twitter = [
                            'tokenId'           => $verify_session->tokenId,
                            'sender_id'         => $sender_id,
                            'text'              => $text,
                            'conversationId'    => $twitter_session->conversationId,
                            'farmId'            => $verify_session->farmId,
                            'payload'           => $request
                        ];

                        DB::table('twitter_conversations')->insert($insert_params_twitter);
                    }
                } else {
                    if ($verify_twitter_email) {
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

                            if (isset($request->users[$sender_id]['name']) && strtolower($request->users[$sender_id]['name']) == "cadu leite") {
                                $sender_name = "Carlos Eduardo Leite";
                                $sender_email = "ceduardo@nuveto.com.br";
                            } else if (isset($request->users[$sender_id]['screen_name']) && strtolower($request->users[$sender_id]['screen_name']) == "alromeiro") {
                                $sender_name = "Andre Romeiro";
                                $sender_email = "alromeiro@nuveto.com.br";
                            } else if (isset($request->users[$sender_id]['name']) && $request->users[$sender_id]['name']) {
                                $sender_name = $request->users[$sender_id]['name'];
                            }

                            $header = [
                                'Content-Type'  => 'application/json',
                                'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                'farmId'        => $create_session['context']['farmId']
                            ];

                            $endpoint = 'conversations';

                            $params = [
                                'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/twitter',
                                'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                'contact' => [
                                    'email' => isset($sender_email) && $sender_email ? $sender_email : 'noreply_' . $sender_id . '@twitter.com',
                                    'firstName' => isset($sender_name) && $sender_name ? $sender_name : 'Twitter User'
                                ],
                                'externalId' => $sender_id,
                                'disableAutoClose' => true,
                                'tenantId' => $create_session['orgId'],
                                'type'  => 'TWITTER'
                            ];

                            $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                            if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                                $insert_params_conversation = [
                                    'tokenId'           => $create_session['tokenId'],
                                    'userId'            => $sender_id,
                                    'conversationId'    => $create_conversation['body']['id'],
                                    'tenantId'          => $create_session['orgId'],
                                    'farmId'            => $create_session['context']['farmId']
                                ];

                                $insert_params_twitter = [
                                    'tokenId'           => $create_session['tokenId'],
                                    'sender_id'         => $sender_id,
                                    'text'              => $text,
                                    'conversationId'    => $create_conversation['body']['id'],
                                    'farmId'            => $create_session['context']['farmId'],
                                    'payload'           => $request
                                ];

                                DB::table('conversation_sessions')->insert($insert_params_conversation);
                                DB::table('twitter_conversations')->insert($insert_params_twitter);
                            }
                        }
                    } else {
                        if (!$first_interation) {
                            $twitter_req = [
                                "text" => "Por questões de Segurança, Informe o seu e-mail para iniciar seu atendimento!",
                                "externalId" => $sender_id,
                                "to" => $recipient_id,
                                "messageId" => $events['id']
                            ];

                            sendMessageTwitter($twitter_req, false, true);
                        } else {
                            $twitter_req = [
                                "text" => "Confirme Abaixo o seu E-mail!",
                                "email" => $text,
                                "externalId" => $sender_id
                            ];

                            sendMessageTwitter($twitter_req, true, false);
                        }

                        $quick_reply = true;
                    }
                }

                if (!$quick_reply) {

                    sendFivenine($sender_id, '', 'twitter');
                }
            }
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }

    public function twitterPing(Request $request)
    {
        // $twitter_secret_key = 'NmfRvhDbIVyBdGbmWTZwbUsmHFBF0k8n9CZGMChnsnPOuX6Cjo';
        // $verify = 'sha256=' . base64_encode(hash_hmac("sha256", $request->crc_token, $twitter_secret_key, true));

        // return response()->json(['response_token' => $verify]);
        if (request()->has('crc_token')) {

            return response()->json(['response_token' => Twitter::crcHash(request()->crc_token)], 200);
        }
    }

    public function removeSub()
    {
        return response()->json([], 200);
    }

    public function addSub()
    {
        return response()->json([], 200);
    }
}
