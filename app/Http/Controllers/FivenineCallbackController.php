<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class fivenineCallbackController extends Controller
{
    public function chatSession(Request $request)
    {
        $faker = Faker::create('pt_BR');

        $insert_params = [
            'name'              => $faker->name(),
            'email'             => $faker->unique()->email(),
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'conversation_id'   => $request['correlationId'],
            'remember_token'    => Str::random(10),
        ];

        DB::table('users')->insert($insert_params);

        return response()->json([], 204);
    }

    public function chatCallback(Request $request)
    {
        // default variables
        $error_msg = null;

        $from_id = DB::table('users')->where('conversation_id', '=', $request['correlationId'])->first();

        if (!$error_msg) {
            // send to database
            $messageID = $request['correlationId'];
            Chatify::newMessage([
                'id' => (string) $messageID,
                'type' => 'API',
                'from_id' => (string) $from_id->id,
                'to_id' => (string) $request['externalId'],
                'body' => $request['text'],
                'attachment' => '',
            ]);

            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);

            Chatify::push('private-chatify', 'messaging', [
                'from_id' => (string) $from_id->id,
                'to_id' => (string) $request['externalId'],
                'message' => Chatify::messageCard($messageData, 'default')
            ]);

            $localParams = [
                'user_id' => $from_id->id,
                'messenger_id' => $messageID,
                'auth_id' => (string) $request['externalId']
            ];

            // localAPI(false, 'callback/updateContactList', 'POST', $localParams);

            $acknowledgeParams = [
                'messages' => [
                    [
                        'type' => 'DELIVERED',
                        'messageId' => $request['messageId']
                    ]
                ]
            ];

            sendFivenine($from_id->id, '', 'chat', 'put', '/messages/acknowledge', $acknowledgeParams, $request['externalId']);
        }


        // send the response
        return response()->json([
            'status' => '200',
            'error' => $error_msg ? 1 : 0,
            'error_msg' => $error_msg,
            'message' => 'Menssagem enviada com Sucesso!',
        ]);
    }

    public function chatTerminate(Request $request)
    {
        // DB::table('users')->where('conversation_id', $request['correlationId'])->delete();
        DB::table('conversation_sessions')->where('conversationId', '=', $request['correlationId'])->update(['terminate' => '1']);

        return response()->json([], 204);
    }

    public function chatAccept(Request $request)
    {
        DB::table('users')->where('conversation_id', $request['correlationId'])->update(['name' => $request['displayName']]);

        return response()->json([], 204);
    }

    public function chatTyping(Request $request)
    {
        return response()->json([], 204);
    }

    /**
     * Update user's list item data
     *
     * @param Request $request
     * @return JSON response
     */
    public function updateContactItem(Request $request)
    {
        $users = DB::table('users')->join('messages', 'to_id', '=', 'users.id')->get()->unique('id');

        if ($users->count() > 0) {
            // fetch contacts
            $contacts = null;
            foreach ($users as $user) {
                if ($user->id != $request['auth_id']) {
                    // Get user data
                    // $userCollection = User::where('id', $user->id)->first();
                    $userCollection = DB::table('users')->where('id', '=', $user->id)->orWhere('conversation_id', '=', $user->id)->first();

                    $contacts .= Chatify::getContactItem($request['messenger_id'], $userCollection, $request['auth_id']);
                }
            }
        }

        // send the response
        return response()->json([
            'contacts' => $users->count() > 0 ? $contacts : '<br><p class="message-hint"><span>Lista de Contatos Vazia!</span></p>',
        ], 200);
    }
}
