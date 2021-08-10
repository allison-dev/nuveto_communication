<div class="row clearfix">
    <div class="col-sm-4" style="text-align: center">
        <label for="network">@lang('system.network')</label>
        <div class="form-group">
            <select class="form-control show-tick ms selectpicker {{ $errors->has('network') ? 'is-invalid' : '' }}"
                name="network" id="network" data-placeholder="Selecione">
                @if (!is_null($billing))
                    <option value="Chat" {{ strtolower($billing->network) == 'chat' ? 'selected="selected"' : '' }}>
                        @lang('system.chat')
                    </option>
                    <option value="Facebook"
                        {{ strtolower($billing->network) == 'facebook' ? 'selected="selected"' : '' }}>
                        @lang('system.facebook')
                    </option>
                    <option value="Reclame_Aqui"
                        {{ strtolower($billing->network) == 'reclame_aqui' ? 'selected="selected"' : '' }}>
                        @lang('system.reclame_aqui')
                    </option>
                    <option value="Twitter"
                        {{ strtolower($billing->network) == 'twitter' ? 'selected="selected"' : '' }}>
                        @lang('system.twitter')
                    </option>
                    <option value="Whatsapp"
                        {{ strtolower($billing->network) == 'whatsapp' ? 'selected="selected"' : '' }}>
                        @lang('system.whatsapp')
                    </option>
                @else
                    <option value="Chat" {{ old('network') == 'chat' ? 'selected="selected"' : '' }}>
                        @lang('system.chat')
                    </option>
                    <option value="Facebook" {{ old('network') == 'facebook' ? 'selected="selected"' : '' }}>
                        @lang('system.facebook')
                    </option>
                    <option value="Reclame_Aqui" {{ old('network') == 'reclame_aqui' ? 'selected="selected"' : '' }}>
                        @lang('system.reclame_aqui')
                    </option>
                    <option value="Twitter" {{ old('network') == 'twitter' ? 'selected="selected"' : '' }}>
                        @lang('system.twitter')
                    </option>
                    <option value="Whatsapp" {{ old('network') == 'whatsapp' ? 'selected="selected"' : '' }}>
                        @lang('system.whatsapp')
                    </option>
                @endif
            </select>
            @if ($errors->has('network'))
                <label id="network-error" class="error"
                    for="network"><strong>{{ $errors->first('network') }}</strong></label>
            @endif
        </div>
    </div>
    <div class="col-sm-4" style="text-align: center">
        <label for="sessions">@lang('system.sessions')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('sessions') ? 'is-invalid' : '' }}" type="sessions"
                id="sessions" placeholder="@lang('system.inform_the_m', ['value' => trans('system.sessions')])"
                value="{{ !is_null($billing) ? $billing->sessions : old('sessions') }}" name="sessions" />
            @if ($errors->has('sessions'))
                <label id="sessions-error" class="error"
                    for="sessions"><strong>{{ $errors->first('sessions') }}</strong></label>
            @endif
        </div>
    </div>
    <div class="col-sm-4" style="text-align: center">
        <label for="price">@lang('system.price')</label>
        <div class="form-group">
            <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" type="price" id="price"
                placeholder="@lang('system.inform_the_m', ['value' => trans('system.price')])"
                value="{{ !is_null($billing) ? $billing->price : old('price') }}" name="price" />
            @if ($errors->has('price'))
                <label id="price-error" class="error"
                    for="price"><strong>{{ $errors->first('price') }}</strong></label>
            @endif
        </div>
    </div>
</div>
