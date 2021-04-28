<?php

namespace App\Http\Controllers;

use App\Jobs\SendReclameAqui;
use App\Mail\ReclameAquiMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;

class TestController extends Controller
{
    public function index()
    {
        return false;
    }

    public function clientMail()
    {
        $client = Client::account('gmail');
        $client->connect();
        $folders = $client->getFolders();
        $folder = $client->getFolder('INBOX');
        $messages = $folder->query()->unseen()->from('sigmain@nuveto.com.br')->since(now()->subDays(7))->get();

        $messages->each(function ($message) {
            $explode_subject = explode('-', $message->getSubject());
            $subject = $explode_subject[0];
            $id = $explode_subject[1];
            $text_body = $message->gethtmlBody();
            $explode_body = explode('Responda Acima desta Linha', $text_body);
            $body = explode('To:', $explode_body[0]);
            $response = trim(preg_replace('/\s\s+/', '', $body[0]));
            $RAResponse = [
                'externalId'    => $id,
                'text'          => $response
            ];
            dd('teste');
            // SendReclameAqui::dispatch($RAResponse)->delay(now()->addSeconds('30'));
        });
    }

    public function sendMailHtml()
    {
        $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', '43365572')->where('customer_id', '=', '57491713')->first();

        if (($verify_send)) {
            $send_info = [
                'ticket_id'         => $verify_send->id,
                'customer_name'     => $verify_send->customer_name,
                'customer_email'    => $verify_send->customer_email,
                'complaint_title'   => $verify_send->complaint_title,
                'complaint_content' => $verify_send->complaint_content,
                'icon'              => asset('vendor/adminlte/dist/img/icone_ReclameAqui.png'),
                'header_img'        => asset(config('adminlte.mail_img_header')),
                'footer_img'        => asset(config('adminlte.mail_img_footer')),
                'salute'            => salute(),
                'network'           => 'Reclame Aqui'
            ];
        }
        return new ReclameAquiMail($send_info);
    }
}
