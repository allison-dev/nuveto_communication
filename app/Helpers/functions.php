<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Facades\ChatifyMessenger as Chatify;
use Carbon\Carbon;
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
        } else if ($channel == 'reclame_aqui') {

            $data = DB::table('reclame_aqui_conversation')->where('ticket_id', '=', $id)->orderBy('id', 'desc')->first();

            if (!is_null($data)) {
                $message = $data->text;
                $external_id = $data->ticket_id;
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
            Log::error(json_encode($parameters));
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
                'id'                => $data['messageId'],
                'type'              => 'twitter',
                'from_id'           => $data['externalId'],
                'to_id'             => $data['to'],
                "first_interation"  => 1,
                "created_at"        => Carbon::now()
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

        $page_token = env('FACEBOOK_PAGE_TOKEN', 'EAAMNolX1ZCDUBAMbLyPXt1F0nRofs7jnZA4rxEoI1M3SvaMDyqjhtstpZCjWbhKW6h5qqyLWBgbWZAcORsBdswGTmlnPV2HK1BAe6ZCCcHqPESspxU2TCQ7UT3sUM8lWv0YyzsbpfYvRhR8DZCwNP9DltgEnrYcAdaZBoCL6T1pFmkZCA78Vum00');

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

            Log::alert(json_encode($get_token));

            if (isset($get_token['accessToken']) && !empty($get_token['accessToken'])) {
                $botmaker_token = $get_token['accessToken'];
            } else {
                $db_token = DB::table('setting')->where('channel', '=', 'whatsapp')->first();
                if (!empty($db_token->refreshToken)) {
                    $botmaker_token = $db_token->refreshToken;
                } else {
                    $botmaker_token = $db_token->whatsappdefaulttoken;
                }
            }

            DB::table('setting')->where('secretId', '=', $config->secretId)->where('channel', '=', 'whatsapp')->update(['refreshToken' => $botmaker_token, "updated_at" => Carbon::now()]);

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

if (!function_exists('sendMessageReclameAqui')) {
    function sendMessageReclameAqui($request, $type = 'pub')
    {
        $return = [];

        $config = DB::table('setting')->where('channel', '=', 'reclame_aqui')->first();

        if (!empty($config->clientId) && !empty($config->secretId)) {
            $header = [
                'clientId'      => $config->clientId,
                'secretId'      => $config->secretId,
                'Content-Type'  => 'application/json',
            ];

            $get_token = getReclameAquiToken($header, 'auth/oauth/token?grant_type=client_credentials');

            DB::table('setting')->where('secretId', '=', $config->secretId)->where('channel', '=', 'reclame_aqui')->update(['refreshToken' => $get_token['access_token'], "updated_at" => Carbon::now()]);

            $reclame_aqui_token = $get_token['access_token'];

            $baseUrl = 'https://app.hugme.com.br/api/';

            if ($type == 'priv') {

                $endpoint = 'ticket/v1/tickets/message/private';

                $url = $baseUrl . $endpoint;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('id' => $request->externalId, 'email' => $request->sender, 'message' => $request->text),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: multipart/form-data',
                        'Authorization: Bearer ' . $reclame_aqui_token
                    ),
                ));
            } else {

                $endpoint = 'ticket/v1/tickets/message/public';

                $url = $baseUrl . $endpoint;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('id' => $request->externalId, 'message' => $request->text),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: multipart/form-data',
                        'Authorization: Bearer ' . $reclame_aqui_token
                    ),
                ));
            }

            $response = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $content = json_decode($response, true);

            Log::notice(json_encode($content));
            Log::notice(json_encode($type));

            if ($code != 200) {

                $return = [
                    'success' => false,
                    'code'    => $code,
                    'body'    => $content,
                ];
            } else {

                $return = $content;
            }

            Log::debug(json_encode($return));
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

        Log::critical(json_encode($header));

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

        $page_token = env('FACEBOOK_PAGE_TOKEN', 'EAAMNolX1ZCDUBAMbLyPXt1F0nRofs7jnZA4rxEoI1M3SvaMDyqjhtstpZCjWbhKW6h5qqyLWBgbWZAcORsBdswGTmlnPV2HK1BAe6ZCCcHqPESspxU2TCQ7UT3sUM8lWv0YyzsbpfYvRhR8DZCwNP9DltgEnrYcAdaZBoCL6T1pFmkZCA78Vum00');

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

if (!function_exists('menuActive')) {

    function menuActive($list)
    {
        $class = '';

        if (in_array(str_replace('-', '_', request()->segment(2)), $list, true)) {
            $class = 'active open';
        }
        return $class;
    }
}

if (!function_exists('cashFormat')) {

    function cashFormat($value, $decimals = 2, $p = ',', $p2 = '.')
    {
        return number_format($value, $decimals, $p, $p2);
    }
}

if (!function_exists('formatMask')) {

    function formatMask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] === '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }
}

if (!function_exists('daysWeek')) {
    function daysWeek()
    {
        return [
            [
                'value' => 'Sunday',
                'name'  => 'Domingo',
            ],
            [
                'value' => 'Monday',
                'name'  => 'Segunda-Feira',
            ],
            [
                'value' => 'Tuesday',
                'name'  => 'Terça-Feira',
            ],
            [
                'value' => 'Wednesday',
                'name'  => 'Quarta-feira',
            ],
            [
                'value' => 'Thursday',
                'name'  => 'Quinta-feira',
            ],
            [
                'value' => 'Friday',
                'name'  => 'Sexta-feira',
            ],
            [
                'value' => 'Saturday',
                'name'  => 'Sábado',
            ],
        ];
    }
}

if (!function_exists('postomonAPI')) {
    function postomonAPI($header, $url, $method = 'get', $parameters = false)
    {
        $data = [
            'headers' => $header,
        ];
        if ($parameters) {
            $data['parameters'] = json_encode($parameters);
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

if (!function_exists('getReclameAquiToken')) {
    function getReclameAquiToken($header, $endpoint, $method = 'post', $parameters = false)
    {
        $baseUrl = 'https://app.hugme.com.br/api/';

        $client_id = $header['clientId'];
        $secret_id = $header['secretId'];

        unset($header['clientId'], $header['secretId']);

        $url = $baseUrl . $endpoint;

        $data = [
            'headers' => $header,
        ];

        if ($parameters) {
            $data['body'] = json_encode($parameters);
        }

        $client = new Client([
            'auth' => [$client_id, $secret_id],
        ]);

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

if (!function_exists('getReclameAquiTickets')) {
    function getReclameAquiTickets($page = 1)
    {
        $return = [];

        $config = DB::table('setting')->where('channel', '=', 'reclame_aqui')->first();

        if (!empty($config->clientId) && !empty($config->secretId)) {
            $header = [
                'clientId'      => $config->clientId,
                'secretId'      => $config->secretId,
                'Content-Type'  => 'application/json',
            ];

            $get_token = getReclameAquiToken($header, 'auth/oauth/token?grant_type=client_credentials');

            DB::table('setting')->where('secretId', '=', $config->secretId)->where('channel', '=', 'reclame_aqui')->update(['refreshToken' => $get_token['access_token'], "updated_at" => Carbon::now()]);

            $reclame_aqui_token = $get_token['access_token'];

            $baseUrl = 'https://app.hugme.com.br/api/v1/';

            $lte_date = Carbon::now()->toISOString();
            $gte_date = Carbon::now()->subMonths(1)->toISOString();

            $endpoint = 'tickets?last_modification_date[gte]=' . $gte_date . '&last_modification_date[lte]=' . $lte_date . '&page[size]=1&page[number]=' . $page . '&sort[creation_date]=DESC&hugme_status.id[in]=1,16,21';

            //$endpoint = 'tickets?id[eq]=44172884';

            $url = $baseUrl . $endpoint;

            $data = [
                'headers' => [
                    'Content-Type'      => 'application/json',
                    'Accept'            => "application/json",
                    'Accept-Encoding'   => 'gzip, deflate, br',
                    'Connection'        => 'keep-alive',
                    "Authorization"     => "Bearer " . $reclame_aqui_token
                ]
            ];

            $method = 'get';

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
            return $return;
        }
    }
}
if (!function_exists('fiveNineSend')) {
    function fiveNineSend($request)
    {
        if (isset($request['id']) && isset($request['complaint_content'])) {

            $ticket_id = $request['id'];
            $text = $request['complaint_content'];
            $name = $request['customer']['name'];
            $email = $request['customer']['email'][0];
            if (isset($request['customer']['phone_numbers']) && $request['customer']['phone_numbers']) {
                foreach ($request['customer']['phone_numbers'] as $phone_numbers) {
                    $i = 1;
                    $number[$i] = $phone_numbers;
                    $i++;
                }
            }

            $config = DB::table('setting')->where('channel', '=', 'reclame_aqui')->first();

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
                    'callbackUrl' => isset($config->callbackUrl) && !empty($config->callbackUrl) ? $config->callbackUrl : 'https://sigmademo.nuvetoapps.com.br/facebook',
                    'campaignName' => isset($config->campaignName) && !empty($config->campaignName) ? $config->campaignName : 'Chat_Nuveto',
                    'contact' => [
                        'firstName' => isset($name) ? trim($name) : 'Reclame Aqui User',
                        'number1' => isset($number[1]) ? '+55' . $number[1] : '+5511999999999',
                        'number2' => isset($number[2]) ? '+55' . $number[2] : '+5511999999999',
                        'email' => isset($email) && $email ? $email : 'noreply@reclameaqui.com.br',
                    ],
                    'externalId' => $ticket_id,
                    'disableAutoClose' => true,
                    'tenantId' => $create_session['orgId'],
                ];

                $create_conversation = apiCall($header, $endpoint, 'POST', $params);

                if (isset($create_conversation['body']['id']) && $create_conversation['body']['id']) {
                    $insert_params_conversation = [
                        'tokenId'           => $create_session['tokenId'],
                        'userId'            => $ticket_id,
                        'conversationId'    => $create_conversation['body']['id'],
                        'tenantId'          => $create_session['orgId'],
                        'farmId'            => $create_session['context']['farmId'],
                        'channel'           => 'reclame_aqui',
                        "created_at"        =>  Carbon::now()
                    ];

                    $insert_params_reclame_aqui = [
                        'tokenId'           => $create_session['tokenId'],
                        'ticket_id'         => $ticket_id,
                        'text'              => $text,
                        'conversationId'    => $create_conversation['body']['id'],
                        'farmId'            => $create_session['context']['farmId'],
                        'payload'           => json_encode($request),
                        "created_at"        => Carbon::now()
                    ];

                    DB::table('conversation_sessions')->insert($insert_params_conversation);
                    DB::table('reclame_aqui_conversation')->insert($insert_params_reclame_aqui);
                }
            }

            sendFivenine($ticket_id, '', 'reclame_aqui');
        }

        return response()->json(['success' => true, 'response' => 'Menssagem enviada ao Agente'], 200);
    }
}
if (!function_exists('salute')) {
    function salute($name = '')
    {
        date_default_timezone_set('America/Sao_Paulo');
        $hora = date('H');
        if ($hora >= 6 && $hora <= 12)
            return 'Bom dia' . (empty($name) ? '' : ', ' . $name);
        else if ($hora > 12 && $hora <= 18)
            return 'Boa tarde' . (empty($name) ? '' : ', ' . $name);
        else
            return 'Boa noite' . (empty($name) ? '' : ', ' . $name);
    }
}

if (!function_exists('minify_html')) {
    function minify_html($input)
    {
        if (trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if (strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        if (strpos($input, '</style>') !== false) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
                return '<style' . $matches[1] . '>' . minify_css($matches[2]) . '</style>';
            }, $input);
        }
        if (strpos($input, '</script>') !== false) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
                return '<script' . $matches[1] . '>' . minify_js($matches[2]) . '</script>';
            }, $input);
        }

        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
            $input
        );
    }
}

if (!function_exists('minify_css')) {
    function minify_css($input)
    {
        if (trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
            $input
        );
    }
}

if (!function_exists('minify_js')) {
    // JavaScript Minifier
    function minify_js($input)
    {
        if (trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
            $input
        );
    }
}



if (function_exists('remoteIP')) {
    function remoteIP()
    {
        return (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('sendModeration')) {

    function sendModeration($request)
    {
        $return = [];

        $config = DB::table('setting')->where('channel', '=', 'reclame_aqui')->first();

        if (!empty($config->clientId) && !empty($config->secretId)) {
            $header = [
                'clientId'      => $config->clientId,
                'secretId'      => $config->secretId,
                'Content-Type'  => 'application/json',
            ];

            $get_token = getReclameAquiToken($header, 'auth/oauth/token?grant_type=client_credentials');

            DB::table('setting')->where('secretId', '=', $config->secretId)->where('channel', '=', 'reclame_aqui')->update(['refreshToken' => $get_token['access_token'], "updated_at" => Carbon::now()]);

            $reclame_aqui_token = $get_token['access_token'];

            $baseUrl = 'https://app.hugme.com.br/api/';

            $endpoint = 'ticket/v1/tickets/moderation';

            $url = $baseUrl . $endpoint;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('id' => $request->ticket_id, 'reason' => $request->reason, 'message' => $request->message),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: multipart/form-data',
                    'Authorization: Bearer ' . $reclame_aqui_token
                ),
            ));

            $response = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            $content = json_decode($response, true);

            if (substr($code, 0, 1) == '2') {

                $return = [
                    'success' => true,
                    'code'    => $code,
                    'body'    => $content,
                ];
            } else {

                $return = [
                    'success' => false,
                    'code'    => $code,
                    'body'    => $content,
                ];
            }

            Log::notice(json_encode($return));

            return $return;
        }
    }
}
