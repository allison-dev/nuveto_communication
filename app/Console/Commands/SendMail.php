<?php

namespace App\Console\Commands;

use App\Jobs\SendMail as JobsSendMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail {network : Canal de Atendimento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando Usado para disparar o Cron do Sigma.';

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
        $network = $this->argument('network');

        $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', '43365572')->where('customer_id', '=', '57491713')->first();

        if (($verify_send)) {
            $send_info = [
                'ticket_id'         => $verify_send->ticket_id,
                'customer_name'     => $verify_send->customer_name,
                'customer_email'    => $verify_send->customer_email,
                'complaint_title'   => $verify_send->complaint_title . '-' . $verify_send->ticket_id,
                'complaint_content' => $verify_send->complaint_content,
                'icon'              => asset('vendor/adminlte/dist/img/icone_ReclameAqui.png'),
                'header_img'        => asset(config('adminlte.mail_img_header')),
                'footer_img'        => asset(config('adminlte.mail_img_footer')),
                'salute'            => salute(),
                'network'           => str_replace('_', ' ', $network)
            ];

            JobsSendMail::dispatch($send_info)->delay(now()->addSeconds('15'));
        }
        /* if (strtolower(str_replace('_', ' ', $network)) == 'reclame aqui') {
            $retriveTickets = getReclameAquiTickets();
            $send_info = [];

            foreach ($retriveTickets['data'] as $ticket) {

                $startTime  = Carbon::parse($ticket['creation_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s');
            $finishTime = Carbon::now();
            $diffTime = $finishTime->diffInHours($startTime);
            $diffDays = $finishTime->diffInDays($startTime);
                $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', $ticket['id'])->where('customer_id', '=', $ticket['customer']['id'])->first('send');

                if (is_null($verify_send)) {
                    $insert_params_reclame_aqui = [
                        'ticket_id'         => $ticket['id'],
                        'customer_id'       => $ticket['customer']['id'],
                        'customer_name'     => $ticket['customer']['name'],
                        'customer_email'    => $ticket['customer']['email'][0],
                        'customer_phones'   => json_encode($ticket['customer']['phone_numbers']),
                        'complaint_title'   => $ticket['complaint_title'],
                        'complaint_content' => $ticket['complaint_content'],
                        "created_at"        => Carbon::parse($ticket['creation_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s'),
                        "updated_at"        => Carbon::parse($ticket['last_modification_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s')
                    ];

                    DB::table('reclame_aqui')->insert($insert_params_reclame_aqui);

                    $send_info = [
                        'ticket_id'         => $ticket['id'],
                        'customer_name'     => $ticket['customer']['name'],
                        'customer_email'    => $ticket['customer']['email'][0],
                        'complaint_title'   => $ticket['complaint_title'],
                        'complaint_content' => $ticket['complaint_content'],
                        'icon'              => asset('vendor/adminlte/dist/img/icone_ReclameAqui.png'),
                        'header_img'        => asset(config('adminlte.mail_img_header')),
                        'footer_img'        => asset(config('adminlte.mail_img_footer')),
                        'salute'            => salute(),
                        'network'           => str_replace('_', ' ', $network)
                    ];

                    JobsSendMail::dispatch($send_info)->delay(now()->addSeconds('15'));
                }
            }
        } else {
            $this->error('network não encontrada!');
            return false;
        } */
    }
}
