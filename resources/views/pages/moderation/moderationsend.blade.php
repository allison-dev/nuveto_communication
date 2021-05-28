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
            <h1>Moderação Enviada</h1>
            <span>{{$moderation['message']}}</span>
        </div>
    </div>
</div>
