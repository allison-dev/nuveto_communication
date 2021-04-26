<?php

namespace App\Console\Commands;

use App\Jobs\SendReclameAqui;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Webklex\IMAP\Facades\Client;

class RetrieveMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando Usado para Verificar novos E-mail no Sigma';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = Client::account('gmail');
        $client->connect();
        $folders = $client->getFolders();
        $folder = $client->getFolder('INBOX');
        $messages = $folder->query()->from('sigmaout@nuveto.com.br')->since(now()->subDays(5))->get();

        $messages->each(function ($message) {
            $explode_subject = explode('-', $message->getSubject());
            $subject = $explode_subject[0];
            $id = $explode_subject[1];
            $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', $id)->first('send');
            if (isset($verify_send) && $verify_send->send == 0) {
                $text_body = $message->gethtmlBody();
                $explode_body = explode('Responda Acima desta Linha', $text_body);
                $body = explode('Em', $explode_body[0]);
                $response = strip_tags($body[0]);
                $RAResponse = [
                    'externalId'    => $id,
                    'text'          => $response
                ];
                DB::table('reclame_aqui')->where('ticket_id', '=', $id)->update(['send' => 1]);
                SendReclameAqui::dispatch($RAResponse)->delay(now()->addSeconds('15'));
            }
        });
    }
}
