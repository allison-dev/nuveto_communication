<?php

namespace App\Console\Commands;

use App\Jobs\SendMail as JobsSendMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
        $redis = Redis::connection();
        $page = $redis->get('reclame_aqui_page');
        $last_page = $redis->get('reclame_aqui_last_page');
        $next_page = $redis->get('reclame_aqui_next_page');

        if (is_null($page)) {
            $page = 1;
        }

        if (is_null($last_page)) {
            $last_page = 100;
        }

        if (is_null($next_page)) {
            $next_page = 0;
        } else {
            $page = $next_page;
        }

        $network = $this->argument('network');

        if (strtolower(str_replace('_', ' ', $network)) == 'reclame aqui') {
            if ($last_page >= $next_page) {
                $retriveTickets = getReclameAquiTickets($page);
                $pages_total = ($retriveTickets['meta']['total'] / $retriveTickets['meta']['page']['size']);
                $last_page = (int) ceil($pages_total);
                $next_page = $page + 1;
                $redis->set('reclame_aqui_page', $page);
                $redis->set('reclame_aqui_next_page', $next_page);
                $redis->set('reclame_aqui_last_page', $last_page);
                $send_info = [];

                foreach ($retriveTickets['data'] as $ticket) {
                    /*
                        $startTime  = Carbon::parse($ticket['creation_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s');
                        $finishTime = Carbon::now();
                        $diffTime = $finishTime->diffInHours($startTime);
                        $diffDays = $finishTime->diffInDays($startTime);
                    */
                    $verify_send = DB::table('reclame_aqui')->where('ticket_id', '=', $ticket['id'])->where('customer_id', '=', $ticket['customer']['id'])->orderBy('id', 'desc')->first('send');

                    if (is_null($verify_send)) {
                        $insert_params_reclame_aqui = [
                            'ticket_id'         => $ticket['id'],
                            'send'              => 1,
                            'customer_id'       => $ticket['customer']['id'],
                            'customer_name'     => $ticket['customer']['name'],
                            'customer_email'    => $ticket['customer']['email'][0],
                            'customer_phones'   => json_encode($ticket['customer']['phone_numbers']),
                            'complaint_title'   => str_replace('-', ' ', $ticket['complaint_title']) . '-' . $ticket['id'],
                            'complaint_content' => $ticket['complaint_content'],
                            "created_at"        => Carbon::parse($ticket['creation_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s'),
                            "updated_at"        => Carbon::parse($ticket['last_modification_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s')
                        ];

                        DB::table('reclame_aqui')->insert($insert_params_reclame_aqui);

                        $send_info = [
                            'ticket_id'         => $ticket['id'],
                            'customer_name'     => $ticket['customer']['name'],
                            'customer_email'    => $ticket['customer']['email'][0],
                            'complaint_title'   => str_replace('-', ' ', $ticket['complaint_title']) . '-' . $ticket['id'],
                            'complaint_content' => $ticket['complaint_content'],
                            'icon'              => asset('vendor/adminlte/dist/img/icone_ReclameAqui.png'),
                            'header_img'        => asset(config('adminlte.mail_img_header')),
                            'footer_img'        => asset(config('adminlte.mail_img_footer')),
                            'salute'            => salute(),
                            'network'           => str_replace('_', ' ', $network)
                        ];

                        JobsSendMail::dispatch($send_info)->delay(now()->addSeconds('15'));
                    } else {
                        $verify_response = DB::table('mail_responses')->where('external_id', '=', $ticket['id'])->where('network', '=', strtolower(str_replace('_', ' ', $network)))->orderBy('id', 'desc')->first();

                        if (!is_null($verify_response)) {
                            $insert_params_reclame_aqui = [
                                'ticket_id'         => $ticket['id'],
                                'send'              => 1,
                                'customer_id'       => $ticket['customer']['id'],
                                'customer_name'     => $ticket['customer']['name'],
                                'customer_email'    => $ticket['customer']['email'][0],
                                'customer_phones'   => json_encode($ticket['customer']['phone_numbers']),
                                'complaint_title'   => str_replace('-', ' ', $ticket['complaint_title']) . '-' . $ticket['id'],
                                'complaint_content' => $ticket['complaint_content'],
                                "created_at"        => Carbon::parse($ticket['creation_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s'),
                                "updated_at"        => Carbon::parse($ticket['last_modification_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s')
                            ];

                            DB::table('reclame_aqui')->insert($insert_params_reclame_aqui);

                            $send_info = [
                                'ticket_id'         => $ticket['id'],
                                'customer_name'     => $ticket['customer']['name'],
                                'customer_email'    => $ticket['customer']['email'][0],
                                'complaint_title'   => str_replace('-', ' ', $ticket['complaint_title']) . '-' . $ticket['id'],
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
                }
            } else {
                $redis->del('reclame_aqui_page');
                $redis->del('reclame_aqui_last_page');
                $redis->del('reclame_aqui_next_page');
            }
        } else {
            $this->error('network não encontrada!');
            return false;
        }
    }
}
