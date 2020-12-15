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
    function sendFivenine($id, $message = '', $channel = 'chat')
    {
        if ($channel == 'twitter') {

            $data = DB::table('twitter_conversations')->where('sender_id', '=', $id)->orderBy('id', 'desc')->first();
            $message = $data->text;
            $external_id = $data->sender_id;
            $token_id = $data->tokenId;
            $farm_id = $data->farmId;
            $conversation_id = $data->conversationId;
        } else if ($channel == 'facebook') {

            $data = DB::table('facebook_conversations')->where('sender_id', '=', $id)->orderBy('id', 'desc')->first();
            $message = $data->text;
            $external_id = $data->sender_id;
            $token_id = $data->tokenId;
            $farm_id = $data->farmId;
            $conversation_id = $data->conversationId;
        } else {

            $data = DB::table('users')->where('id', '=', $id)->first();
            $conversation_session = DB::table('conversation_sessions')->where('conversationId', '=', $data->conversation_id)->first();
            $external_id = Auth::user()->id;
            $token_id = $conversation_session->tokenId;
            $farm_id = $conversation_session->farmId;
            $conversation_id = $conversation_session->conversationId;
        }

        $header = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer-' . $token_id,
            'farmId'        => $farm_id
        ];

        $endpoint = 'conversations/' . $conversation_id . '/messages';

        $params = [
            'message'    => $message,
            'externalId' => $external_id,
        ];

        $log = [
            'header' => $header,
            'endpoint' => $endpoint,
            'params' => $params
        ];

        Log::debug(json_encode($log));

        $response = apiCall($header, $endpoint, 'POST', $params);

        Log::debug(json_encode($response));
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
    function sendMessageTwitter($data)
    {
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

        $response = Twitter::postDm($params);

        $log = [
            "response"  => $response,
            "params"    => $params
        ];

        Log::debug(json_encode($log));
    }
}

if (!function_exists('sendMessagefacebook')) {
    function sendMessagefacebook($request)
    {
        $return = [];

        $baseUrl = 'https://graph.facebook.com/';

        $version = '9.0/';

        $page_token = env('FACEBOOK_PAGE_TOKEN', 'EAAnrDZBZALHKwBANeSnZAbealn57yTm4v5GwXzv2lEKS57r9qlnXnZB7k9KhVBZCfVb8JSvwAcriuf2XJOF82ZCiAcWKztuOgDa2JmsDXbqmHgH6fDcWlCO4DrRbmIbD332eKmwcUzZA1ZClQlUUk3Ha7Gz11U03HZAWZB0Q1KmZCotegZDZD');

        $endpoint = 'messages?access_token=' . $page_token;

        $url = $baseUrl . $version . $endpoint;

        $data = [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ];

        $params = [
            "recipient" => [
                "id"    => $request->externalId
            ],
            "message"   => [
                "text"  => $request->text
            ]
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
