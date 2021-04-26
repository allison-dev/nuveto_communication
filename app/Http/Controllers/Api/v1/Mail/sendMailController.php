<?php

namespace App\Http\Controllers\Api\v1\Mail;

use App\Http\Controllers\Api\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendMailController extends BaseController
{
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        $verify_code = sha1('Nuveto Sigma Verify');

        if ($verify_code == request('sigma_id')) {

            if (strtolower($request['network']) == 'reclame aqui') {
                $RAResponse = [
                    'externalId'    => $request->id,
                    'text'          => $request->response
                ];
                sendMessageReclameAqui($RAResponse);

                $insert_params_response = [
                    'external_id'   => $request->id,
                    'network'       => 'reclame_aqui',
                    'subject'       => $request->subject,
                    'body'          => $request->body,
                    'response'      => $request->response,
                    "created_at"    => Carbon::now()
                ];

                DB::table('mail_responses')->insert($insert_params_response);
            }

            return redirect()->away(request('redirect'));
        }
    }
}
