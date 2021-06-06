<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <label for="ticket_id">@lang('system.ticket_id')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('ticket_id') ? 'is-invalid' : '' }}" type="text" id="ticket_id"
                placeholder="@lang('system.inform_the_m', ['value' => trans('system.ticket_id')])"
                value="{{ !is_null($evaluation['ticket_id']) ? $evaluation['ticket_id'] : old('ticket_id') }}"
                name="ticket_id" />
            @if ($errors->has('ticket_id'))
                <label id="ticket_id-error" class="error"
                    for="ticket_id"><strong>{{ $errors->first('ticket_id') }}</strong></label>
            @endif
        </div>
    </div>
</div>
