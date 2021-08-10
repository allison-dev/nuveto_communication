<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    public function index($fileName)
    {
        return $fileName;
    }

    public function getImages($fileName)
    {
        return Storage::disk('s3')->response($fileName);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasfile('file')) {
            $file = $request->file('file');
            $imageName = time() . $file->getClientOriginalName();
            $result = Storage::disk('s3')->put(str_replace(' ', '-', $imageName), file_get_contents($file));
            $host = $request->getSchemeAndHttpHost();

            $info = DB::table('medias')->where('conversationId', '=', $request->conversationId)->orderBy('id', 'desc')->first();

            if ($result) {
                switch (strtolower($info->channel)) {
                    case 'chat':
                        $externalId = DB::table('chat_conversations')->where('conversationId', '=', $info->conversationId)->orderBy('id', 'desc')->first();
                        $data = (object) [
                            "displayName"   => "Sigma",
                            "externalId"    => $externalId->clientId,
                            "text"          => $imageName,
                            "attachment"    => $host . '/anexos/images/' . str_replace(' ', '-', $imageName)
                        ];
                        sendMessageChat($data, true);
                        break;
                    case 'facebook':
                        # code...
                        break;
                    case 'twitter':
                        # code...
                        break;
                    case 'reclame_aqui':
                        # code...
                        break;
                    case 'whatsapp':
                        $externalId = DB::table('whatsapp_conversations')->where('conversationId', '=', $info->conversationId)->orderBy('id', 'desc')->first();
                        $data = (object) [
                            "displayName"   => "Sigma",
                            "externalId"    => $externalId->sender_phone,
                            "text"          => $imageName,
                            "attachment"    => $host . '/anexos/images/' . str_replace(' ', '-', $imageName)
                        ];
                        sendMessageWhatsapp($data, true);
                        break;
                    default:
                        # code...
                        break;
                }
                return response()->json([], 204);
            }
        }
    }
}
