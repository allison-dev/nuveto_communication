<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacebookCallbackController extends Controller
{
    public function facebookSession()
    {
        return response()->json([], 204);
    }

    public function facebookMessageCallback(Request $request)
    {
        sendMessageFacebook($request);

        $insert_params_messages = [
            'id'        => $request->correlationId,
            'type'      => 'facebook',
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

        sendFivenine($request->externalId, '', 'facebook', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);

        return response()->json(['success' => true, 'data' => 'Menssagem Respondida pelo Agente!'], 200);
    }

    public function facebookTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

        DB::table('bot_interations')->where('sender_id', '=', $request['externalId'])->update(['terminate' => '1', 'send_five9' => '0']);

        return response()->json([], 204);
    }

    public function facebookTyping(Request $request)
    {
        return response()->json([], 204);
    }

    public function facebookAccept(Request $request)
    {
        return response()->json([], 204);
    }

    public function facebookCallback(Request $request)
    {
        $quick_reply = false;
        $sender_email = false;

        if (isset($request->entry)) {
            $data = $request->entry;

            $events = $data[0];

            if (isset($events['messaging'][0]['sender']['id']) && isset($events['messaging'][0]['message']['text'])) {

                $sender_id = $events['messaging'][0]['sender']['id'];

                $text = $events['messaging'][0]['message']['text'];

                $verify_page = DB::table('setting')->where('facebook_page_id', '=', $sender_id)->first();

                if (is_null($verify_page)) {

                    $config = DB::table('setting')->where('channel', '=', 'facebook')->first();

                    $facebook_session = DB::table('facebook_conversations')->where('sender_id', '=', $sender_id)->orderBy('id', 'desc')->first();

                    $bot_session = DB::table('bot_interations')->where('sender_id', '=', $sender_id)->where('terminate', '=', 0)->orderBy('id', 'desc')->first();

                    if (!$bot_session) {
                        DB::table('bot_interations')->insert(['sender_id' => $sender_id]);
                    } else {
                        DB::table('bot_interations')->where('terminate', '=', 0)->update(['sender_id' => $sender_id]);
                    }

                    if (isset($bot_session->bot_order) && $bot_session->bot_order) {
                        $bot_order = $bot_session->bot_order;
                    } else {
                        $bot_order = 0;
                    }

                    if (isset($bot_session->sender_email) && $bot_session->sender_email) {
                        $sender_email = $bot_session->sender_email;
                    }

                    if (isset($bot_session->send_five9) && $bot_session->send_five9) {
                        $send_five9 = $bot_session->send_five9;
                    }

                    if (isset($events['messaging'][0]['message']['quick_reply']['payload'])) {
                        $verify_facebook_email = true;
                        $sender_email = $events['messaging'][0]['message']['quick_reply']['payload'];
                    } else {
                        $verify_facebook_email = false;
                    }

                    $bot_interations = DB::table('bot_interations')->where('order', '=', $bot_order)->first();

                    $getSenderInfo = getMessengerInfo($sender_id);

                    if (isset($facebook_session->conversationId)) {

                        $verify_session = DB::table('conversation_sessions')->where('conversationId', $facebook_session->conversationId)->where('terminate', '=', '0')->first();

                        if (!$verify_session) {
                            /* Create Session */

                            if ($verify_facebook_email) {

                                if (!$send_five9) {
                                    if (isset($bot_interations) && $bot_interations) {
                                        if (!empty($bot_interations->options)) {

                                            $bot_options = json_decode($bot_interations->options, true);

                                            foreach ($bot_options['options'] as $options) {
                                                $text_options[] = [
                                                    'content_type' => 'text',
                                                    'title' => $options['label'],
                                                    'payload' => $options['variable'] . ":" . $options['choice']
                                                ];
                                            }

                                            $facebook_req = [
                                                "text" => $bot_interations->text,
                                                "externalId" => $sender_id
                                            ];

                                            sendMessageFacebook($facebook_req, true, 'text', $text_options);
                                        }

                                        DB::table('bot_interations')->where('terminate', '=', 0)->where('sender_id', '=', $sender_id)->update(['bot_order' => $bot_order]);
                                    } else {

                                        $text_options[] = [
                                            'content_type' => 'text',
                                            "title" => "Nuveto",
                                            "payload" => 'five9',
                                        ];

                                        $facebook_req = [
                                            "text" => $bot_interations->text,
                                            "externalId" => $sender_id
                                        ];

                                        sendMessageFacebook($facebook_req, true, 'text', $text_options);
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

                                        if (isset($getSenderInfo['name']) && strtolower($getSenderInfo['name']) == "cadu leite") {
                                            $getSenderInfo['name'] = "Carlos Eduardo Leite";
                                            $sender_email = "ceduardo@nuveto.com.br";
                                        } else if (isset($sender_email) && strtolower($sender_email) == "alromeiro@gmail.com") {
                                            $getSenderInfo['name'] = "Andre Romeiro";
                                            $sender_email = "alromeiro@nuveto.com.br";
                                        }

                                        $header = [
                                            'Content-Type'  => 'application/json',
                                            'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                            'farmId'        => $create_session['context']['farmId']
                                        ];

                                        $endpoint = 'conversations';

                                        $params = [
                                            'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/facebook',
                                            'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                            'contact' => [
                                                'email' => isset($sender_email) ? $sender_email : 'noreply_' . $sender_id . '@facebook.com',
                                                'firstName' => isset($getSenderInfo['name']) ? $getSenderInfo['name'] : 'Facebook User',
                                                'socialAccountName' => isset($getSenderInfo['name']) ? $getSenderInfo['name'] : 'Facebook User',
                                                'socialAccountProfileUrl' => isset($getSenderInfo['profile_pic']) ? $getSenderInfo['profile_pic'] : 'https://www.facebook.com/profile.php?id=' . $sender_id,
                                                'gender' => isset($getSenderInfo['gender']) ? $getSenderInfo['gender'] : 'N',
                                            ],
                                            'externalId' => $sender_id,
                                            'disableAutoClose' => true,
                                            'tenantId' => $create_session['orgId'],
                                            'type'  => 'FACEBOOK'
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

                                            $insert_params_facebook = [
                                                'tokenId'           => $create_session['tokenId'],
                                                'sender_id'         => $sender_id,
                                                'text'              => $text,
                                                'conversationId'    => $create_conversation['body']['id'],
                                                'farmId'            => $create_session['context']['farmId'],
                                                'farmId'            => $create_session['context']['farmId'],
                                                'payload'           => $request
                                            ];

                                            DB::table('conversation_sessions')->insert($insert_params_conversation);
                                            DB::table('facebook_conversations')->insert($insert_params_facebook);
                                        }
                                    }
                                }
                            } else {

                                $facebook_req = [
                                    "text" => "Por questões de Segurança, clique abaixo para confirmar o seu e-mail!",
                                    "externalId" => $sender_id
                                ];

                                sendMessageFacebook($facebook_req, true);

                                $quick_reply = true;
                            }
                        } else {
                            $insert_params_facebook = [
                                'tokenId'           => $verify_session->tokenId,
                                'sender_id'         => $sender_id,
                                'text'              => $text,
                                'conversationId'    => $facebook_session->conversationId,
                                'farmId'            => $verify_session->farmId,
                                'payload'           => $request
                            ];

                            DB::table('facebook_conversations')->insert($insert_params_facebook);
                        }
                    } else {

                        /* Create Session */

                        if ($verify_facebook_email) {

                            if (!$send_five9) {
                                if (isset($bot_interations) && $bot_interations) {
                                    if (!empty($bot_interations->options)) {

                                        $bot_options = json_decode($bot_interations->options, true);

                                        foreach ($bot_options['options'] as $options) {
                                            $text_options[] = [
                                                'content_type' => 'text',
                                                'title' => $options['label'],
                                                'payload' => $options['variable'] . ":" . $options['choice']
                                            ];
                                        }

                                        $facebook_req = [
                                            "text" => $bot_interations->text,
                                            "externalId" => $sender_id
                                        ];

                                        sendMessageFacebook($facebook_req, true, 'text', $text_options);
                                    }

                                    DB::table('bot_interations')->where('terminate', '=', 0)->where('sender_id', '=', $sender_id)->update(['bot_order' => $bot_order]);
                                } else {

                                    $text_options[] = [
                                        'content_type' => 'text',
                                        "title" => "Nuveto",
                                        "payload" => 'five9',
                                    ];

                                    $facebook_req = [
                                        "text" => $bot_interations->text,
                                        "externalId" => $sender_id
                                    ];

                                    sendMessageFacebook($facebook_req, true, 'text', $text_options);
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

                                    if (isset($getSenderInfo['name']) && strtolower($getSenderInfo['name']) == "cadu leite") {
                                        $getSenderInfo['name'] = "Carlos Eduardo Leite";
                                        $sender_email = "ceduardo@nuveto.com.br";
                                    } else if (isset($sender_email) && strtolower($sender_email) == "alromeiro@gmail.com") {
                                        $getSenderInfo['name'] = "Andre Romeiro";
                                        $sender_email = "alromeiro@nuveto.com.br";
                                    }

                                    $header = [
                                        'Content-Type'  => 'application/json',
                                        'Authorization' => 'Bearer-' . $create_session['tokenId'],
                                        'farmId'        => $create_session['context']['farmId']
                                    ];

                                    $endpoint = 'conversations';

                                    $params = [
                                        'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/facebook',
                                        'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                        'contact' => [
                                            'email' => isset($sender_email) ? $sender_email : 'noreply_' . $sender_id . '@facebook.com',
                                            'firstName' => isset($getSenderInfo['name']) ? $getSenderInfo['name'] : 'Facebook User',
                                            'socialAccountName' => isset($getSenderInfo['name']) ? $getSenderInfo['name'] : 'Facebook User',
                                            'socialAccountProfileUrl' => isset($getSenderInfo['profile_pic']) ? $getSenderInfo['profile_pic'] : 'https://www.facebook.com/profile.php?id=' . $sender_id,
                                            'gender' => isset($getSenderInfo['gender']) ? $getSenderInfo['gender'] : 'N',
                                        ],
                                        'externalId' => $sender_id,
                                        'disableAutoClose' => true,
                                        'tenantId' => $create_session['orgId'],
                                        'type'  => 'FACEBOOK'
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

                                        $insert_params_facebook = [
                                            'tokenId'           => $create_session['tokenId'],
                                            'sender_id'         => $sender_id,
                                            'text'              => $text,
                                            'conversationId'    => $create_conversation['body']['id'],
                                            'farmId'            => $create_session['context']['farmId'],
                                            'payload'           => $request
                                        ];

                                        DB::table('conversation_sessions')->insert($insert_params_conversation);
                                        DB::table('facebook_conversations')->insert($insert_params_facebook);
                                    }
                                }
                            }
                        } else {
                            $facebook_req = [
                                "text" => "Por questões de Segurança, clique abaixo para confirmar o seu e-mail!",
                                "externalId" => $sender_id
                            ];

                            sendMessageFacebook($facebook_req, true);

                            $quick_reply = true;
                        }
                    }

                    if ($send_five9) {

                        sendFivenine($sender_id, '', 'facebook');
                    }
                }
            }
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }

    public function facebookPing(Request $request)
    {
        $verify_token = "2b78f505bb3ad1a0c5d45c956b969c966dd4b788";

        $mode = $request['hub_mode'];
        $token = $request['hub_verify_token'];
        $challenge = $request['hub_challenge'];

        if ($mode && $token) {

            if ($mode === 'subscribe' && $token === $verify_token) {
                return response($challenge, 200);
            }
        }
    }
}
