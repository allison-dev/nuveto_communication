<meta http-equiv="refresh" content="10">
<div class="body">
    <ul class="nav nav-tabs p-0 mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#general">
                @lang('system.general')
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane in active" id="general">
            @if (isset($evaluation['ticket_id']))
                <h1>Ticket : {{$evaluation['ticket_id']}}</h1>
                <span>{{ $evaluation['message'] }}</span>
            @else
                <h1>Avaliação Não Enviada</h1>
                <span>{{ $evaluation['message'] }}</span>
            @endif
        </div>
    </div>
</div>
