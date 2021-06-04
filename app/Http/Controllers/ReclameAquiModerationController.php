<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModerationRequest;
use Illuminate\Http\Request;

class ReclameAquiModerationController extends Controller
{
    public function index(Request $request)
    {
        $moderation = [
            'reasons' => [
                '1'     => 'A Reclamação de outra empresa',
                '4'     => 'Reclamação em duplicidade',
                '5'     => 'Conteúdo imprório',
                '7'     => 'Reclamação infundada',
                '15'    => 'Reclamação de terceiros',
                '16'    => 'Reclamação trabalhista',
                '17'    => 'A empresa não violou o direito do consumidor',
                '19'    => 'Este é um caso de fraude',
            ],
            'ticket_id' => isset($request->ticketid) && $request->ticketid ? $request->ticketid : null,
        ];
        return view('pages.moderation.index')->with(compact(['moderation']));
    }

    public function SendModeration(ModerationRequest $request)
    {
        $request->validated();

        $result = sendModeration($request);

        $moderation = [
            'reasons' => [
                '1'     => 'A Reclamação de outra empresa',
                '4'     => 'Reclamação em duplicidade',
                '5'     => 'Conteúdo imprório',
                '7'     => 'Reclamação infundada',
                '15'    => 'Reclamação de terceiros',
                '16'    => 'Reclamação trabalhista',
                '17'    => 'A empresa não violou o direito do consumidor',
                '19'    => 'Este é um caso de fraude',
            ],
            'ticket_id' => isset($request->ticketid) && $request->ticketid ? $request->ticketid : null,
            'message'   => $result['body']['message']
        ];

        return view('pages.moderation.index')->with(compact(['moderation']));
    }
}
