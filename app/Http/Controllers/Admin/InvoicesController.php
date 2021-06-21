<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoicesRequest;
use App\Services\InvoicesService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
    private $invoicesService;

    public function __construct(InvoicesService $invoicesService)
    {
        $this->invoicesService = $invoicesService;
    }

    public function index()
    {
        $invoices = $this->invoicesService->index();
        if ($invoices) {

            $invoices->company = DB::table('companies')->first();
        }
        return view('pages.admin.invoices.index')->with(compact(['invoices']));
    }

    public function generate(InvoicesRequest $request)
    {
        $facebook_sessions = 0;
        $twitter_sessions = 0;
        $whatsapp_sessions = 0;
        $reclame_aqui_sessions = 0;
        $get_billing_facebook = false;
        $get_billing_twitter = false;
        $get_billing_whatsapp = false;
        $get_billing_reclame_aqui = false;

        $request->validated();

        $get_billings = DB::table('billings')->get();

        foreach ($get_billings as $billings) {
            $network = strtolower($billings->network);
            switch ($network) {
                case 'facebook':
                    $facebook_sessions = DB::table(DB::raw('sig_conversation_sessions cs,
                    sig_conversation_sessions cs2'))->whereRaw('cs.terminate = 1')->whereRaw('cs.channel = "' . $network . '"')->whereRaw('cs.channel = cs2.channel')->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') >= '" . $request['ini_date'] . "'")->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') <= '" . $request['end_date'] . "'")->whereRaw('cs.created_at BETWEEN cs2.created_at AND cs2.updated_at')->groupBy(DB::raw('cs.created_at'))->orderBy(DB::raw('cs.created_at'))->get([DB::raw('cs.created_at'), DB::raw('cs.channel'), DB::raw('COUNT(*) AS CountSimultaneous')])->max('CountSimultaneous');
                    $get_billing_facebook = DB::table('billings')->where('network', '=', $network)->first();
                    $totals[$network] = $facebook_sessions * $get_billing_facebook->price;
                    break;
                case 'twitter':
                    $twitter_sessions = DB::table(DB::raw('sig_conversation_sessions cs,
                    sig_conversation_sessions cs2'))->whereRaw('cs.terminate = 1')->whereRaw('cs.channel = "' . $network . '"')->whereRaw('cs.channel = cs2.channel')->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') >= '" . $request['ini_date'] . "'")->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') <= '" . $request['end_date'] . "'")->whereRaw('cs.created_at BETWEEN cs2.created_at AND cs2.updated_at')->groupBy(DB::raw('cs.created_at'))->orderBy(DB::raw('cs.created_at'))->get([DB::raw('cs.created_at'), DB::raw('cs.channel'), DB::raw('COUNT(*) AS CountSimultaneous')])->max('CountSimultaneous');
                    $get_billing_twitter = DB::table('billings')->where('network', '=', $network)->first();
                    $totals[$network] = $twitter_sessions * $get_billing_twitter->price;
                    break;
                case 'whatsapp':
                    $whatsapp_sessions = DB::table(DB::raw('sig_conversation_sessions cs,
                    sig_conversation_sessions cs2'))->whereRaw('cs.terminate = 1')->whereRaw('cs.channel = "' . $network . '"')->whereRaw('cs.channel = cs2.channel')->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') >= '" . $request['ini_date'] . "'")->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') <= '" . $request['end_date'] . "'")->whereRaw('cs.created_at BETWEEN cs2.created_at AND cs2.updated_at')->groupBy(DB::raw('cs.created_at'))->orderBy(DB::raw('cs.created_at'))->get([DB::raw('cs.created_at'), DB::raw('cs.channel'), DB::raw('COUNT(*) AS CountSimultaneous')])->max('CountSimultaneous');
                    $get_billing_whatsapp = DB::table('billings')->where('network', '=', $network)->first();
                    $totals[$network] = $whatsapp_sessions * $get_billing_whatsapp->price;
                    break;
                case 'reclame_aqui':
                    $reclame_aqui_sessions = DB::table(DB::raw('sig_conversation_sessions cs,
                    sig_conversation_sessions cs2'))->whereRaw('cs.terminate = 1')->whereRaw('cs.channel = "' . $network . '"')->whereRaw('cs.channel = cs2.channel')->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') >= '" . $request['ini_date'] . "'")->whereRaw("DATE_FORMAT(cs.created_at, '%Y-%m-%d') <= '" . $request['end_date'] . "'")->whereRaw('cs.created_at BETWEEN cs2.created_at AND cs2.updated_at')->groupBy(DB::raw('cs.created_at'))->orderBy(DB::raw('cs.created_at'))->get([DB::raw('cs.created_at'), DB::raw('cs.channel'), DB::raw('COUNT(*) AS CountSimultaneous')])->max('CountSimultaneous');
                    $get_billing_reclame_aqui = DB::table('billings')->where('network', '=', $network)->first();
                    $totals[$network] = $reclame_aqui_sessions * $get_billing_reclame_aqui->price;
                    break;
                default:
                    break;
            }
        }

        $get_company = DB::table('companies')->first();

        $get_address = DB::table('addresses')->where('id', '=', $get_company->address_id)->first();

        $subtotal = array_sum($totals);

        $full_total = $subtotal;

        $invoice_id = random_int(1000000, 9999999);

        $invoice_obj = (object)array_merge_recursive((array)$get_company, (array)$get_address, ['facebook_sessions' => $facebook_sessions, 'twitter_sessions' => $twitter_sessions, 'whatsapp_sessions' => $whatsapp_sessions, 'reclame_aqui_sessions' => $reclame_aqui_sessions, 'get_billing_facebook' => $get_billing_facebook, 'get_billing_twitter' => $get_billing_twitter, 'get_billing_whatsapp' => $get_billing_whatsapp, 'get_billing_reclame_aqui' => $get_billing_reclame_aqui, 'invoice_id' => $invoice_id, 'date_ini' => $request['ini_date'], 'date_end' => $request['end_date'], 'total' => json_encode($totals), 'subtotal' => $subtotal, 'full_total' => $full_total]);

        $invoice_obj->birthday = date('d/m/Y', strtotime($invoice_obj->birthday));
        $invoice_obj->date_ini = date('d/m/Y', strtotime($invoice_obj->date_ini));
        $invoice_obj->date_end = date('d/m/Y', strtotime($invoice_obj->date_end));

        $invoices = $invoice_obj;

        $insert_params = [
            'address_id'    => $invoices->address_id,
            'number_home'   => $invoices->number_home,
            'name'          => $invoices->name,
            'email'         => $invoices->email,
            'cellphone'     => $invoices->cellphone,
            'cpf_cnpj'      => $invoices->cpf_cnpj,
            'birthday'      => $invoices->birthday,
            'street'        => $invoices->street,
            'neighborhood'  => $invoices->neighborhood,
            'city'          => $invoices->city,
            'uf'            => $invoices->uf,
            'postcode'      => $invoices->postcode,
            'invoice_id'    => $invoices->invoice_id,
            'date_ini'      => $invoices->date_ini,
            'date_end'      => $invoices->date_end,
            'total'         => $invoices->total,
            'subtotal'      => $invoices->subtotal,
            'full_total'    => $invoices->full_total,
            'created_at'    => Carbon::now()
        ];

        DB::table('invoices')->insert($insert_params);

        $invoice = DB::table('invoices')->where('invoice_id', '=', $invoice_id)->orderBy('id', 'desc')->first();
        $invoices->total = json_decode($invoice->total);

        return view('pages.admin.invoices.generate')->with(compact(['invoices']));
    }
}
