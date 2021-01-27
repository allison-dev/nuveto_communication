<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Thujohn\Twitter\Facades\Twitter;

if (!function_exists('sendFivenine')) {
    function sendFivenine($id, $message = '', $channel = 'chat', $method = 'post', $sendUrl = '/messages', $parameter = false, $external_auth_id = false)
    {
        $send = false;

        if ($channel == 'twitter') {

            $data = DB::table('twitter_conversations')->where('sender_id', '=', $id)->orderBy('id', 'desc')->first();

            if (!is_null($data)) {
                $message = $data->text;
                $external_id = $data->sender_id;
                $token_id = $data->tokenId;
                $farm_id = $data->farmId;
                $conversation_id = $data->conversationId;
                $send = true;
            }
        } else if ($channel == 'facebook') {

            $data = DB::table('facebook_conversations')->where('sender_id', '=', $id)->orderBy('id', 'desc')->first();

            if (!is_null($data)) {
                $message = $data->text;
                $external_id = $data->sender_id;
                $token_id = $data->tokenId;
                $farm_id = $data->farmId;
                $conversation_id = $data->conversationId;
                $send = true;
            }
        } else if ($channel == 'whatsapp') {

            $data = DB::table('whatsapp_conversations')->where('sender_phone', '=', $id)->orderBy('id', 'desc')->first();

            if (!is_null($data)) {
                $message = $data->text;
                $external_id = $data->sender_phone;
                $token_id = $data->tokenId;
                $farm_id = $data->farmId;
                $conversation_id = $data->conversationId;
                $send = true;
            }
        } else {

            $data = DB::table('users')->where('id', '=', $id)->first();

            if (!is_null($data)) {
                $conversation_session = DB::table('conversation_sessions')->where('conversationId', '=', $data->conversation_id)->first();
                $external_id = $external_auth_id ? $external_auth_id : Auth::user()->id;
                $token_id = $conversation_session->tokenId;
                $farm_id = $conversation_session->farmId;
                $conversation_id = $conversation_session->conversationId;
                $send = true;
            }
        }

        if ($send) {
            $header = [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer-' . $token_id,
                'farmId'        => $farm_id
            ];

            $baseUrl = 'conversations/' . $conversation_id;

            $endpoint = $baseUrl . $sendUrl;

            if ($parameter) {
                $params = $parameter;
            } else {
                $params = [
                    'message'    => $message,
                    'externalId' => $external_id,
                ];
            }

            $log = [
                'header' => $header,
                'endpoint' => $endpoint,
                'params' => $params
            ];

            Log::debug(json_encode($log));

            $response = apiCall($header, $endpoint, $method, $params);

            Log::debug(json_encode($response));
        }
    }
}

if (!function_exists('apiCall')) {

    function apiCall($header, $endpoint, $method = 'get', $parameters = false)
    {
        $baseUrl = 'https://app-atl.five9.com/appsvcs/rs/svc/';

        $url = $baseUrl . $endpoint;

        $data = [
            'headers' => $header,
        ];

        if ($parameters) {
            $data['body'] = json_encode($parameters);
        }

        $client = new Client();

        $error = false;

        $msg = false;

        try {

            $response = $client->{$method}($url, $data);
        } catch (ClientException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (ServerException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (RequestException $e) {

            $error = true;
            $msg = $e->getMessage();
        }

        if ($error) {

            return [
                'success' => false,
                'body'    => $msg,
            ];
        }

        $content = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 204) {

            return [
                'success' => false,
                'code'    => $response->getStatusCode(),
                'body'    => $content,
            ];
        }

        return $content;
    }
}

if (!function_exists('sendChatCallback')) {
    function sendChatCallback($data)
    {
        // default variables
        $error_msg = null;

        $from_id = DB::table('users')->where('conversation_id', '=', $data['correlationId'])->first();

        if (!$error_msg) {
            // send to database
            $messageID = $data['correlationId'];
            Chatify::newMessage([
                'id' => (string) $messageID,
                'type' => 'API',
                'from_id' => (string) $from_id->id,
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

if (!function_exists('sendMessageTwitter')) {
    function sendMessageTwitter($data, $quick_reply = false, $first_interation = false, $options = false)
    {
        if ($quick_reply) {

            if (!$options) {
                $options = [
                    [
                        "label" => $data['email'],
                    ],
                    [
                        "label" => "Trocar E-mail."
                    ]
                ];
            }

            $params = [
                "type"              => "message_create",
                "message_create"    => [
                    "target"    => [
                        "recipient_id" => $data['externalId']
                    ],
                    "message_data"   => [
                        "text" => $data['text'],
                        "quick_reply" =>
                        [
                            "type" => "options",
                            "options" => $options
                        ]

                    ]
                ]
            ];
        } else if ($first_interation) {
            $params = [
                "type"              => "message_create",
                "message_create"    => [
                    "target"    => [
                        "recipient_id" => $data['externalId']
                    ],
                    "message_data"   => [
                        "text" => $data['text'],
                    ]
                ]
            ];

            $insert_params_messages = [
                'id'        => $data['messageId'],
                'type'      => 'twitter',
                'from_id'   => $data['externalId'],
                'to_id'     => $data['to'],
                "first_interation" => 1
            ];

            DB::table('messages')->insert($insert_params_messages);
        } else {
            $params = [
                "type"              => "message_create",
                "message_create"    => [
                    "target"    => [
                        "recipient_id" => $data->externalId
                    ],
                    "message_data"   => [
                        "text" => $data->text
                    ]
                ]
            ];
        }

        $response = Twitter::postDm($params);

        $log = [
            "response"  => $response,
            "params"    => $params
        ];

        Log::debug(json_encode($log));
    }
}

if (!function_exists('sendMessageFacebook')) {
    function sendMessageFacebook($request, $quick_reply = false, $type = 'email', $reply_options = false)
    {
        $return = [];

        $baseUrl = 'https://graph.facebook.com/';

        $version = 'v9.0/';

        $page_token = env('FACEBOOK_PAGE_TOKEN', 'EAAMNolX1ZCDUBAFSThAJwEjMVqYZBEZAu0ui0KmZCP6NfaAIXIXCQ3oF0k2hOxQILRNmdZAcYCZCMDv4cH9gGdzBHPeu144MoNI9q1rEcPO0oPPLkX5NwahsKs4dQ3yU3ib51t5YaRZBWxiOE9i3mVtpDyxpXHgot8ysThZBL6qeoRAhJ9h0F4Kz');

        $endpoint = 'me/messages?access_token=' . $page_token;

        $url = $baseUrl . $version . $endpoint;

        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ];

        if ($quick_reply) {
            if ($type == 'email') {
                $params = [
                    "recipient" => [
                        "id"    => $request['externalId']
                    ],
                    "message"   => [
                        "text"  => $request['text'],
                        "quick_replies" => [
                            [
                                "content_type" => "user_email"
                            ]
                        ]
                    ]
                ];
            } else if ($type == "text") {
                $params = [
                    "recipient" => [
                        "id"    => $request['externalId']
                    ],
                    "message"   => [
                        "text"  => $request['text'],
                        "quick_replies" => $reply_options
                    ]
                ];
            }
        } else {
            $params = [
                "recipient" => [
                    "id"    => $request->externalId
                ],
                "message"   => [
                    "text"  => $request->text
                ]
            ];
        }

        if ($params) {
            $data['body'] = json_encode($params);
        }

        $method = 'POST';

        $client = new Client();

        $error = false;

        $msg = false;

        try {

            $response = $client->{$method}($url, $data);
        } catch (ClientException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (ServerException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (RequestException $e) {

            $error = true;
            $msg = $e->getMessage();
        }

        if ($error) {
            $return = [
                'success' => false,
                'body'    => $msg,
            ];
        } else {
            $content = json_decode($response->getBody(), true);

            if ($response->getStatusCode() != 200) {

                $return = [
                    'success' => false,
                    'code'    => $response->getStatusCode(),
                    'body'    => $content,
                ];
            } else {

                $return = $content;
            }
        }

        $data['url'] = $url;

        Log::debug(json_encode($return));

        Log::debug(json_encode($data));
    }
}

if (!function_exists('sendMessageWhatsapp')) {
    function sendMessageWhatsapp($request)
    {
        $return = [];

        $config = DB::table('setting')->where('channel', '=', 'whatsapp')->first();

        if (!empty($config->clientId) && !empty($config->secretId) && !empty($config->refreshToken) && !empty($config->whatsapp_phone)) {
            $header = [
                'clientId'      => $config->clientId,
                'secretId'      => $config->secretId,
                'refreshToken'  => $config->refreshToken,
            ];

            $get_token = getBotmakerToken($header, 'auth/credentials');

            DB::table('setting')->where('secretId', '=', $config->secretId)->where('channel', '=', 'whatsapp')->update(['refreshToken' => $get_token['refreshToken']]);

            $botmaker_token = $get_token['accessToken'];

            $baseUrl = 'https://go.botmaker.com/api/';

            $version = 'v1.0/';

            $endpoint = 'message/v3';

            $url = $baseUrl . $version . $endpoint;

            $data = [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'access-token'  => $botmaker_token,
                ]
            ];

            $params = [
                'chatPlatform'      => 'whatsapp',
                'chatChannelNumber' => $config->whatsapp_phone,
                'platformContactId' => $request->externalId,
                'messageText'       => $request->text
            ];

            if ($params) {
                $data['body'] = json_encode($params);
            }

            $method = 'POST';

            $client = new Client();

            $error = false;

            $msg = false;

            try {

                $response = $client->{$method}($url, $data);
            } catch (ClientException $e) {

                $error = true;
                $msg = $e->getMessage();
            } catch (ServerException $e) {

                $error = true;
                $msg = $e->getMessage();
            } catch (RequestException $e) {

                $error = true;
                $msg = $e->getMessage();
            }

            if ($error) {
                $return = [
                    'success' => false,
                    'body'    => $msg,
                ];
            } else {
                $content = json_decode($response->getBody(), true);

                if ($response->getStatusCode() != 200) {

                    $return = [
                        'success' => false,
                        'code'    => $response->getStatusCode(),
                        'body'    => $content,
                    ];
                } else {

                    $return = $content;
                }
            }

            $data['url'] = $url;

            Log::debug(json_encode($return));

            Log::debug(json_encode($data));
        }
    }
}

if (!function_exists('getBotmakerToken')) {
    function getBotmakerToken($header, $endpoint, $method = 'post', $parameters = false)
    {
        $baseUrl = 'https://go.botmaker.com/api/v1.0/';

        $url = $baseUrl . $endpoint;

        $data = [
            'headers' => $header,
        ];

        if ($parameters) {
            $data['body'] = json_encode($parameters);
        }

        $client = new Client();

        $error = false;

        $msg = false;

        try {

            $response = $client->{$method}($url, $data);
        } catch (ClientException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (ServerException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (RequestException $e) {

            $error = true;
            $msg = $e->getMessage();
        }

        if ($error) {

            return [
                'success' => false,
                'body'    => $msg,
            ];
        }

        $content = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {

            return [
                'success' => false,
                'code'    => $response->getStatusCode(),
                'body'    => $content,
            ];
        }

        return $content;
    }
}

if (!function_exists('getMessengerInfo')) {
    function getMessengerInfo($sender_id)
    {
        $return = [];

        $baseUrl = 'https://graph.facebook.com/';

        $page_token = env('FACEBOOK_PAGE_TOKEN', 'EAAMNolX1ZCDUBAFSThAJwEjMVqYZBEZAu0ui0KmZCP6NfaAIXIXCQ3oF0k2hOxQILRNmdZAcYCZCMDv4cH9gGdzBHPeu144MoNI9q1rEcPO0oPPLkX5NwahsKs4dQ3yU3ib51t5YaRZBWxiOE9i3mVtpDyxpXHgot8ysThZBL6qeoRAhJ9h0F4Kz');

        $endpoint = '?fields=name,gender,profile_pic&access_token=' . $page_token;

        $url = $baseUrl . $sender_id . $endpoint;

        $method = 'get';

        $client = new Client();

        $error = false;

        $msg = false;

        try {

            $response = $client->{$method}($url);
        } catch (ClientException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (ServerException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (RequestException $e) {

            $error = true;
            $msg = $e->getMessage();
        }

        if ($error) {
            $return = [
                'success' => false,
                'body'    => $msg,
            ];
        } else {
            $content = json_decode($response->getBody(), true);

            if ($response->getStatusCode() != 200) {

                $return = [
                    'success' => false,
                    'code'    => $response->getStatusCode(),
                    'body'    => $content,
                ];
            } else {

                $return = $content;
            }
        }

        $data['url'] = $url;

        Log::debug(json_encode($return));

        Log::debug(json_encode($data));

        return $return;
    }
}

if (!function_exists('localAPI')) {
    function localAPI($header = false, $endpoint, $method = 'get', $parameters = false)
    {
        $baseUrl = env('APP_URL', '');

        $url = $baseUrl . $endpoint;

        if (!$header) {
            $header = [
                'Content-Type' => 'application/json'
            ];
        }

        $data = [
            'headers' => $header,
        ];

        if ($parameters) {
            $data['body'] = json_encode($parameters);
        }

        $client = new Client();

        $error = false;

        $msg = false;

        try {

            $response = $client->{$method}($url, $data);
        } catch (ClientException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (ServerException $e) {

            $error = true;
            $msg = $e->getMessage();
        } catch (RequestException $e) {

            $error = true;
            $msg = $e->getMessage();
        }

        if ($error) {

            return [
                'success' => false,
                'body'    => $msg,
            ];
        }

        $content = json_decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {

            return [
                'success' => false,
                'code'    => $response->getStatusCode(),
                'body'    => $content,
            ];
        }

        return $content;
    }
}
