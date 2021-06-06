<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EvaluationRequest;
use Carbon\Carbon;

class ReclameAquiEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $evaluation = [
            'ticket_id' => isset($request->ticketid) && $request->ticketid ? $request->ticketid : null,
        ];
        return view('pages.evaluation.index')->with(compact(['evaluation']));
    }

    public function SendEvaluation(EvaluationRequest $request)
    {
        $request->validated();

        $ticket_id = isset($request->ticketid) && $request->ticketid ? $request->ticketid : null;

        if ($ticket_id) {

            $get_ticket_info = getReclameAquiTickets(1, $ticket_id);

            if (isset($get_ticket_info['data']) && !empty($get_ticket_info['data'])) {
                $startTime  = Carbon::parse($get_ticket_info['data'][0]['last_modification_date'])->setTimezone('UTC -3')->format('Y-m-d H:m:s');
                $finishTime = Carbon::now();
                $diffTime = $finishTime->diffInHours($startTime);
                $diffDays = $finishTime->diffInDays($startTime);
                if ($diffDays > 3) {

                    if (isset($content['data'][0]['request_evaluation']) && !$content['data'][0]['request_evaluation']) {
                        $result = sendEvaluation($request);
                        $evaluation = [
                            'ticket_id' => $ticket_id,
                            'message'   => $result['body']['message']
                        ];
                    } else {
                        $evaluation = [
                            'ticket_id' => $ticket_id,
                            'message'   => 'Este Ticket já foi enviado para Avaliação!'
                        ];
                    }
                } else {
                    $evaluation = [
                        'ticket_id' => $ticket_id,
                        'message'   => 'A ultima interação deste ticket foi em ' . $diffDays . ' dia(s), portanto não atende aos critérios de avaliação!'
                    ];
                }
            } else {
                $evaluation = [
                    'ticket_id' => $ticket_id,
                    'message'   => 'Este Ticket não atende aos critérios de avaliação!'
                ];
            }
        } else {
            $evaluation = [
                'message'   => 'Ticket ID Não Localizado, verifique o mesmo e tente novamente'
            ];
        }
        return view('pages.evaluation.index')->with(compact(['evaluation']));
    }
}
