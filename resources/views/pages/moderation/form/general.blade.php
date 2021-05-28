<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <label for="ticket_id">@lang('system.ticket_id')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('ticket_id') ? 'is-invalid' : '' }}" type="text" id="ticket_id"
                placeholder="@lang('system.inform_the_m', ['value' => trans('system.ticket_id')])"
                value="{{ !is_null($moderation['ticket_id']) ? $moderation['ticket_id'] : old('ticket_id') }}"
                name="ticket_id" />
            @if ($errors->has('ticket_id'))
                <label id="ticket_id-error" class="error"
                    for="ticket_id"><strong>{{ $errors->first('ticket_id') }}</strong></label>
            @endif
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <label for="reason">@lang('system.reason')</label>
        <div class="form-group">
            <select class="form-control show-tick ms selectpicker {{ $errors->has('reason') ? 'is-invalid' : '' }}"
                name="reason" id="reason" data-placeholder="Selecione">
                <option value="*">
                    -----Selecione a Razão-----
                </option>
                @foreach ($moderation['reasons'] as $key => $reasons)
                    <option value="{{ $key }}" {{ old('reason') == $reasons ? 'selected="selected"' : '' }}>
                        {{ $reasons }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('reason'))
                <label id="reason-error" class="error"
                    for="reason"><strong>{{ $errors->first('reason') }}</strong></label>
            @endif
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <label for="message">@lang('system.message')</label>
        <div class="form-group">
            <textarea class="form-control" name="message" id="message" cols="5" rows="5"></textarea>
        </div>
    </div>
</div>
