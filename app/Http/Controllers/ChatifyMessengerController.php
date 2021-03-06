<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Favorite;
use Pusher\Pusher;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatifyMessengerController extends Controller
{
    /**
     * Allowed extensions to upload attachment
     * [Images / Files]
     *
     * @var
     */
    public static $allowed_images = array('png', 'jpg', 'jpeg', 'gif');
    public static $allowed_files  = array('zip', 'rar', 'txt');

    /**
     * This method returns the allowed image extensions
     * to attach with the message.
     *
     * @return array
     */
    public function getAllowedImages()
    {
        return self::$allowed_images;
    }

    /**
     * This method returns the allowed file extensions
     * to attach with the message.
     *
     * @return array
     */
    public function getAllowedFiles()
    {
        return self::$allowed_files;
    }

    /**
     * Returns an array contains messenger's colors
     *
     * @return array
     */
    public function getMessengerColors()
    {
        return [
            '1' => '#2180f3',
            '2' => '#2196F3',
            '3' => '#00BCD4',
            '4' => '#3F51B5',
            '5' => '#673AB7',
            '6' => '#4CAF50',
            '7' => '#FFC107',
            '8' => '#FF9800',
            '9' => '#f2282b',
            '10' => '#9C27B0',
        ];
    }

    /**
     * Pusher connection
     */
    public function pusher()
    {
        return new Pusher(
            config('chatify.pusher.key'),
            config('chatify.pusher.secret'),
            config('chatify.pusher.app_id'),
            [
                'cluster' => config('chatify.pusher.options.cluster'),
                'useTLS' => config('chatify.pusher.options.useTLS')
            ]
        );
    }

    /**
     * Trigger an event using Pusher
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     * @return void
     */
    public function push($channel, $event, $data)
    {
        return $this->pusher()->trigger($channel, $event, $data);
    }

    /**
     * Authintication for pusher
     *
     * @param string $channelName
     * @param string $socket_id
     * @param array $data
     * @return void
     */
    public function pusherAuth($channelName, $socket_id, $data = [])
    {
        return $this->pusher()->socket_auth($channelName, $socket_id, $data);
    }

    /**
     * Fetch message by id and return the message card
     * view as a response.
     *
     * @param int $id
     * @return array
     */
    public function fetchMessage($id)
    {
        $attachment = $attachment_type = $attachment_title = null;

        $diffMessage = '';

        $msg = DB::table('messages')->where('identification', '=', $id)->orWhere('id', '=', $id)->orderBy('identification', 'desc')->first();

        $user_id = DB::table('users')->where('id', '=', $id)->orWhere('conversation_id', '=', $id)->first();

        if (isset($msg->created_at)) {
            $create_at = Carbon::parse($msg->created_at);

            $diffMessage = $create_at->diffForHumans(Carbon::now());
        } else {
            $create_at = Carbon::now();
        }

        // If message has attachment
        if (isset($msg->attachment) && $msg->attachment) {
            // Get attachment and attachment title
            $att = explode(',', $msg->attachment);
            $attachment       = $att[0];
            $attachment_title = $att[1];

            // determine the type of the attachment
            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
            $attachment_type = in_array($ext, $this->getAllowedImages()) ? 'image' : 'file';
        }

        return [
            'id' => $msg->id,
            'from_id' => $msg->from_id,
            'to_id' => $msg->to_id,
            'message' => $msg->body,
            'attachment' => [$attachment, $attachment_title, $attachment_type],
            'time' => $diffMessage,
            'fullTime' => $msg->created_at,
            'viewType' => $msg->type == 'API' ? 'default' : 'sender',
            'seen' => $msg->seen,
        ];
    }

    /**
     * Return a message card with the given data.
     *
     * @param array $data
     * @param string $viewType
     * @return void
     */
    public function messageCard($data, $viewType = null)
    {
        $data['viewType'] = ($viewType) ? $viewType : $data['viewType'];
        return view('Chatify.layouts.messageCard', $data)->render();
    }

    /**
     * Default fetch messages query between a Sender and Receiver.
     *
     * @param int $user_id
     * @return Collection
     */
    public function fetchMessagesQuery($user_id, $auth_id = false)
    {
        if (isset(Auth::user()->id) && !$auth_id) {

            return DB::table('messages')->where('from_id', '=', Auth::user()->id)->where('to_id', '=', $user_id)->orWhere('from_id', '=', $user_id)->where('to_id', '=', Auth::user()->id);
        } else {

            return DB::table('messages')->where('from_id', '=', $auth_id)->where('to_id', '=', $user_id)->orWhere('from_id', '=', $user_id)->where('to_id', '=', $auth_id);
        }
    }

    /**
     * create a new message to database
     *
     * @param array $data
     * @return void
     */
    public function newMessage($data)
    {
        $message = new Message();
        $message->id = $data['id'];
        $message->type = $data['type'];
        $message->from_id = $data['from_id'];
        $message->to_id = $data['to_id'];
        $message->body = $data['body'];
        $message->attachment = $data['attachment'];
        $message->save();
    }

    /**
     * Make messages between the sender [Auth user] and
     * the receiver [User id] as seen.
     *
     * @param int $user_id
     * @return bool
     */
    public function makeSeen($user_id)
    {
        Message::Where('from_id', $user_id)
            ->where('to_id', Auth::user()->id)
            ->where('seen', 0)
            ->update(['seen' => 1,"updated_at" => Carbon::now()]);
        return 1;
    }

    /**
     * Get last message for a specific user
     *
     * @param int $user_id
     * @return Collection
     */
    public function getLastMessageQuery($user_id, $auth_id = false)
    {
        return $this->fetchMessagesQuery($user_id, $auth_id)->orderBy('created_at', 'DESC')->latest()->first();
    }

    /**
     * Count Unseen messages
     *
     * @param int $user_id
     * @return Collection
     */
    public function countUnseenMessages($user_id, $auth_id = false)
    {
        if ($auth_id) {
            return Message::where('from_id', $user_id)->where('to_id', $auth_id)->where('seen', 0)->count();
        } else {
            return Message::where('from_id', $user_id)->where('to_id', Auth::user()->id)->where('seen', 0)->count();
        }
    }

    /**
     * Get user list's item data [Contact Itme]
     * (e.g. User data, Last message, Unseen Counter...)
     *
     * @param int $messenger_id
     * @param Collection $user
     * @return void
     */
    public function getContactItem($messenger_id, $user, $auth_id = false)
    {
        // get last message

        if (isset($user->id)) {

            $lastMessageText = $this->getLastMessageQuery($user->id, $auth_id);

            $diffMessage = '';

            if (isset($lastMessageText->created_at)) {
                $create_at = Carbon::parse($lastMessageText->created_at);
            } else {
                $create_at = Carbon::now();
            }

            $diffMessage = $create_at->diffForHumans(Carbon::now());

            // Get Unseen messages counter
            $unseenCounter = $this->countUnseenMessages($user->id, $auth_id);

            return view('Chatify.layouts.listItem', [
                'get' => 'users',
                'user' => $user,
                'lastMessage' => $lastMessageText,
                'diffMessage' => $diffMessage,
                'unseenCounter' => $unseenCounter,
                'type' => 'user',
                'id' => $messenger_id,
                'messengerColor' => DB::table('users')->where('id','=',$auth_id)->first(['messenger_color']),
                'auth_id' => $auth_id
            ])->render();
        }
    }

    /**
     * Check if a user in the favorite list
     *
     * @param int $user_id
     * @return boolean
     */
    public function inFavorite($user_id)
    {
        return Favorite::where('user_id', Auth::user()->id)
            ->where('favorite_id', $user_id)->count() > 0
            ? true : false;
    }

    /**
     * Make user in favorite list
     *
     * @param int $user_id
     * @param int $star
     * @return boolean
     */
    public function makeInFavorite($user_id, $action)
    {
        if ($action > 0) {
            // Star
            $star = new Favorite();
            $star->id = rand(9, 99999999);
            $star->user_id = Auth::user()->id;
            $star->favorite_id = $user_id;
            $star->save();
            return $star ? true : false;
        } else {
            // UnStar
            $star = Favorite::where('user_id', Auth::user()->id)->where('favorite_id', $user_id)->delete();
            return $star ? true : false;
        }
    }

    /**
     * Get shared photos of the conversation
     *
     * @param int $user_id
     * @return array
     */
    public function getSharedPhotos($user_id)
    {
        $images = array(); // Default
        // Get messages
        $msgs = $this->fetchMessagesQuery($user_id)->orderBy('created_at', 'DESC');
        if ($msgs->count() > 0) {
            foreach ($msgs->get() as $msg) {
                // If message has attachment
                if ($msg->attachment) {
                    $attachment = explode(',', $msg->attachment)[0]; // Attachment
                    // determine the type of the attachment
                    in_array(pathinfo($attachment, PATHINFO_EXTENSION), $this->getAllowedImages())
                        ? array_push($images, $attachment) : '';
                }
            }
        }
        return $images;
    }

    /**
     * Delete Conversation
     *
     * @param int $user_id
     * @return boolean
     */
    public function deleteConversation($user_id)
    {
        try {
            foreach ($this->fetchMessagesQuery($user_id)->get() as $msg) {
                // delete from database
                $msg->delete();
                // delete file attached if exist
                if ($msg->attachment) {
                    $path = storage_path('app/public/' . config('chatify.attachments.folder') . '/' . explode(',', $msg->attachment)[0]);
                    if (file_exists($path)) {
                        @unlink($path);
                    }
                }
            }
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }
}
