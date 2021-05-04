<?php

namespace App\Http\Controllers;

use App\Jobs\SendReclameAqui;
use App\Mail\ReclameAquiMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;
use Mailjet\Resources;
use App\Jobs\SendMail as JobsSendMail;

class TestController extends Controller
{
    public function index()
    {
        $string = '<html><head></head><body><span style="font-weight: 300;">KFKF</span><b>KFKFK</b><br><br>Teste it<i>alico</i>&nbsp;<div>LDLFASDfadfa s hsdkfjhslkjdfhals dkhdf lsakfj haskdfh lskjahlfkj hslkjf haskjf hlaskdfhalskdfj haslkdf hasldkf ahlsdjkfhaslkjfhsalkjfhakljhdflas halks hfslkajdf hslakjd fhaslkjf haslkdjfh lsakjdf hsljkdfh aslkjdfhaslkfdjhalskdfh sa<br>LDLFASDfadfa s hsdkfjhslkjdfhals dkhdf lsakfj haskdfh lskjahlfkj hslkjf haskjf hlaskdfhalskdfj haslkdf hasldkf ahlsdjkfhaslkjfhsalkjfhakljhdflas halks hfslkajdf hslakjd fhaslkjf haslkdjfh lsakjdf hsljkdfh aslkjdfhaslkfdjhalskdfh sa<br>LDLFASDfadfa s hsdkfjhslkjdfhals dkhdf lsakfj haskdfh lskjahlfkj hslkjf haskjf hlaskdfhalskdfj haslkdf hasldkf ahlsdjkfhaslkjfhsalkjfhakljhdflas halks hfslkajdf hslakjd fhaslkjf haslkdjfh lsakjdf hsljkdfh aslkjdfhaslkfdjhalskdfh sa<br>LDLFASDfadfa s hsdkfjhslkjdfhals dkhdf lsakfj haskdfh lskjahlfkj hslkjf haskjf hlaskdfhalskdfj haslkdf hasldkf ahlsdjkfhaslkjfhsalkjfhakljhdflas halks hfslkajdf hslakjd fhaslkjf haslkdjf<u>h lsakjdf hs</u>ljkdfh aslkjdfhaslkfdjhalskdfh sa</div><div><br></div><div><b>Teste lkjfdçla jçlsk jfçlksdfj çsaldkfj çaslf&nbsp;</b></div><div><br></div><div>Atenciosamente</div><div>Suporte RA<br><hr style="font-weight: 300;"><b style="font-weight: 300;">';
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument;
        $dom->loadHTML($string);

        // Strip wrapping <html> and <body> tags
        $mock = new \DOMDocument;
        $body = $dom->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child) {
            $mock->appendChild($mock->importNode($child, true));
        }

        $fixed = trim($mock->saveHTML());
        dd($fixed);
    }

    public function clientMail()
    {
        $client = Client::account('gmail');
        $client->connect();
        $folders = $client->getFolders();
        $folder = $client->getFolder('INBOX');
        $messages = $folder->query()->from('sigmain@nuveto.com.br')->since(now()->subDays(7))->get();

        $messages->each(function ($message) {
            $explode_subject = explode('-', $message->getSubject());
            $subject = $explode_subject[0];
            $id = $explode_subject[1];
            $text_body = $message->gethtmlBody();
            $explode_body = explode('Responda Acima desta Linha', $text_body);
            $body = explode('<div class="gmail_quote">', $explode_body[0]);
            $strip_body = strip_tags($body[0], '<br><br /><br/>');
            $trim_response = trim(preg_replace('/\s\s+/', '', $strip_body), '<br />');
            $response = nl2br($trim_response);
            $RAResponse = [
                'externalId'    => '43473579',
                'text'          => $response
            ];
            // dd($response);
            SendReclameAqui::dispatch($RAResponse)->delay(now()->addSeconds('5'));
        });
    }

    public function sendMailHtml()
    {
        $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', '43523434')->where('customer_id', '=', '57657707')->first();

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
        JobsSendMail::dispatch($send_info)->delay(now()->addSeconds('5'));
        // return new ReclameAquiMail($send_info);
    }

    function closetags($html)
    {
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);

        $closedtags = $result[1];
        $len_opened = count($openedtags);

        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</' . $openedtags[$i] . '>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    public function gmailClient()
    {
        /* $mail = new MessageMail;
        $mail->subject('test-gmail-client');
        $mail->to('sigmain@nuveto.com.br', 'Sigma - GmailSend');
        $mail->from('all_oli@hotmail.com', 'Allison Oliveira');
        $mail->view('mail.ReclameAqui', [
            'icon'              => asset('vendor/adminlte/dist/img/icone_ReclameAqui.png'),
            'header_img'        => asset(config('adminlte.mail_img_header')),
            'footer_img'        => asset(config('adminlte.mail_img_footer')),
            'customer_name'     => 'Allison Oliveira',
            'customer_email'    => 'all_oli@hotmail.com',
            'complaint_title'   => 'Test Gmail Send',
            'complaint_content' => 'Test Gmail SendTest Gmail SendTest Gmail SendTest Gmail SendTest Gmail SendTest Gmail Send',
            'salute'            => 'Bom Dia',
            'network'           => 'Reclame Aqui',
            'ticket_id'         => '0001123456'
        ]);
        $mail->send(); */
        /* $mj = new \Mailjet\Client('e472da84bdc95ac0ae281a41bf9d8b74', 'bf6d3fa609d8abb840992789859d8390', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "all_oli@hotmail.com",
                        'Name' => "Allison"
                    ],
                    'To' => [
                        [
                            'Email' => "aoliveira@nuveto.com.br",
                            'Name' => "Allison"
                        ]
                    ],
                    'Subject' => "Greetings from Mailjet.",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData()); */

        /* $body = [
            'EmailType' => "unknown",
            'IsDefaultSender' => "false",
            'Name' => "Allison Oliveira",
            'Email' => "all_oli@hotmail.com"
        ]; */
        /* $response = $mj->post(Resources::$SenderValidate, ['id' => '38213']);
        $response->success() && var_dump($response->getData()); */
    }
}
