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
            @include('pages.evaluation.form.general')
        </div>
    </div>
</div>
