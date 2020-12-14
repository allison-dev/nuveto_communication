<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class FacebookCallbackController extends Controller
{
    public function facebookSession()
    {
        return response()->json([], 204);
    }

    public function facebookMessageCallback(Request $request)
    {
        sendMessagefacebook($request);

        $insert_params_messages = [
            'id'        => $request->correlationId,
            'type'      => 'facebook',
            'from_id'   => $request->correlationId,
            'to_id'     => $request->externalId,
        ];

        DB::table('messages')->insert($insert_params_messages);

        return response()->json([], 200);
    }

    public function facebookTerminate(Request $request)
    {
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

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
        if (isset($request->direct_message_events)) {
            $data = $request->entry;

            $events = $data[0];

            $credentials = Socialite::driver('facebook')->user();

            if (isset($events['changes']['value']['from']['id']) && $events['changes']['value']['from']['id'] != $credentials->id) {
                $sender_id = $events['changes']['value']['from']['id'];

                $text = $events['changes']['value']['message'];

                $config = DB::table('conversation_configs')->where('channel', '=', 'facebook')->first();

                $facebook_session = DB::table('facebook_conversations')->where('sender_id', $sender_id)->first();

                if (isset($facebook_session->conversationId)) {
                    $verify_session = DB::table('conversation_sessions')->where('conversationId', $facebook_session->conversationId)->where('terminate', '=', '0')->first();

                    if (!$verify_session) {

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
                                'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'http://nuveto-chat.herokuapp.com/facebook',
                                'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                                'contact' => [
                                    'email' => 'noreply_' . $sender_id . '@facebook.com',
                                    'firstName' => isset($request->users[$sender_id]['name']) ? $request->users[$sender_id]['name'] : 'facebook User'
                                ],
                                'externalId' => $sender_id,
                                'disableAutoClose' => true,
                                'tenantId' => $create_session['orgId'],
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
                            'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'http://nuveto-chat.herokuapp.com/facebook',
                            'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                            'contact' => [
                                'email' => 'noreply_' . $sender_id . '@facebook.com',
                                'firstName' => isset($request->users[$sender_id]['name']) ? $request->users[$sender_id]['name'] : 'facebook User'
                            ],
                            'externalId' => $sender_id,
                            'disableAutoClose' => true,
                            'tenantId' => $create_session['orgId'],
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

                sendFivenine($sender_id, '', 'facebook');
            }
        }

        return response()->json([], 200);
    }

    public function facebookPing(Request $request)
    {
        $verify_token = "2b78f505bb3ad1a0c5d45c956b969c966dd4b788";

        $mode = $request['hub_mode'];
        $token = $request['hub_verify_token'];
        $challenge = $request['hub_challenge'];

        if ($mode && $token) {

            if ($mode === 'subscribe' && $token === $verify_token) {
                return response($challenge,200);
            }
        }
    }
}
