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
        return '{"data":[{"hugme_status":{"id":21,"name":"Novo"},"ra_status":{"id":5,"name":"Não respondido"},"favorable_assessment":{"id":0,"description":"Não Solicitado"},"information_source":{"id":1,"name":"RA Reclamação"},"source":{"id":1,"name":"ReclameAQUI"},"company":{"id":1871,"name":"Koala Testes"},"last_feeling":{"id":-1,"name":null},"feed_type":{"id":-1,"name":null},"moderation":{"user":{"id":-1,"name":null},"status":null,"reason":null,"request_date":null,"response_date":null},"ra":{"replicas_count":0,"source_id":1,"source_name":"Site","deactivation_date":null,"deactivation_reason":null,"internal_process":true,"blackfriday":false},"customer":{"birthday":["1994-05-20T00:00:00.000Z"],"cpf":["43164821816"],"rg":[],"gender":["Masculino"],"email":["all_oli@hotmail.com"],"phone_numbers":["16991461093","16999999999","16993476953"],"photo":[],"id":57090375,"duplicate_id":-1,"name":"Allison Oliveira","cnpj":[],"company_name":"[]","city":[{"id":391,"name":"Franca"}],"state":[{"id":26,"name":"São Paulo","fs":"SP"}],"type":"Private Person","tags":[],"pending_tickets_count":0},"user":{"id":-1,"name":null},"account":{"id":-1,"name":null},"rafone":{"expiration_date":null,"status_id":-1,"status_name":null},"_id":"608bf62deff7550c8e11e723","id":43534294,"source_external_id":"123268927","can_like":false,"commentary":false,"creation_date":"2021-04-30T12:18:30.000Z","insertion_date":"2021-04-30T12:20:49.000Z","complaint_title":"Reclamação Teste 30042021","filed":false,"last_modification_date":"2021-04-30T12:20:49.000Z","closed_date":null,"request_moderation":true,"request_evaluation":false,"frozen":false,"complaint_content":"Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 \n\rReclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 ","ra_reason":"Outros","ra_feeling":null,"complaint_response_content":null,"complaint_response_date":null,"interactions_count":1,"interactions_not_readed_count":9,"resolved_issue":false,"back_doing_business":false,"consumer_consideration":null,"consumer_consideration_date":null,"company_consideration":null,"company_consideration_date":null,"private_treatment_time":null,"public_treatment_time":null,"rating_time":null,"rating":"-1","rating_date":null,"comments_count":0,"redistributions_count":0,"redistributions_reason":null,"ticket_moderations_count":0,"ticket_messages_count":0,"last_replica_date":null,"contact_us":null,"rating_without_response":false,"hugme_ticket_type":null,"customer_interactions_count":9,"company_interactions_count":0,"assignment_count":0,"rule_id":6533,"duplicate_ticket":[],"tags":[],"extra_data":{},"sticky_notes":[],"autos":[],"personalized_fields":[],"attached":[{"id":"355706714","type_detail_id":15,"name":"Anexo","detail_description":"https://raichu-uploads.s3.amazonaws.com/complain_df93e1f8-f19f-4075-8509-88f67026cac6.png","creation_date":"2021-04-30T12:18:30.000Z","privacy":false}],"categories":[],"historical":[{"user":{"id":-1,"name":null,"email":null},"auto":{"id":-1,"name":null},"id":547639628,"creation_date":"2021-04-30T12:20:49.000Z","type":{"id":4,"name":"INTERACAO"}},{"user":{"id":-1,"name":null,"email":null},"auto":{"id":-1,"name":null},"id":547639629,"creation_date":"2021-04-30T12:20:49.000Z","type":{"id":1,"name":"CRIACAO"}}],"interactions":[{"ticket_interaction_id":"160441757","ticket_interaction_type_id":1,"ticket_interaction_name":"Manifestação","customer_id":"57090375","responsible_id":null,"responsible_name":null,"message":"Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 \n\rReclamação Teste 30042021 Reclamação Teste 30042021 Reclamação Teste 30042021 ","privacy":false,"creation_date":"2021-04-30T12:18:30.000Z","modification_date":null,"delivered":true,"readed":false,"visualized":false,"video":null,"picture":null,"details":[{"ticket_detail_id":"355706707","ticket_detail_type_id":1,"name":"Assunto","value":"Outros","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706708","ticket_detail_type_id":14,"name":"Assunto ID","value":"27","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706709","ticket_detail_type_id":8,"name":"IP","value":"187.23.15.41","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706710","ticket_detail_type_id":7,"name":"Para qual empresa deseja reclamar?","value":"Koala Teste","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706711","ticket_detail_type_id":7,"name":"Por qual canal você deseja ser contatado?","value":"E-mail","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706712","ticket_detail_type_id":7,"name":"Você esta na Koala Testes por qual motivo?","value":"Reclamar","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706713","ticket_detail_type_id":3,"name":"Contato 1","value":"16991461093","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706714","ticket_detail_type_id":15,"name":"Anexo","value":"https://raichu-uploads.s3.amazonaws.com/complain_df93e1f8-f19f-4075-8509-88f67026cac6.png","code":null,"creation_date":"2021-04-30T12:18:30.000Z","privacy":false,"modification_date":null},{"ticket_detail_id":"355706715","ticket_detail_type_id":25,"name":"ID Site RA","value":"26DNjjVzgXYI8DUz","code":null,"creation_date":"2021-04-30T12:20:49.000Z","privacy":false,"modification_date":null}]}],"active":true,"duplicate_tiqt":[]}],"meta":{"page":{"number":1,"size":1},"total":1}}';
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
        // JobsSendMail::dispatch($send_info)->delay(now()->addSeconds('5'));
        return new ReclameAquiMail($send_info);
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
