<div class="row clearfix">
    <div class="col-sm-4" style="text-align: center">
		<label for="network">@lang('system.network')</label>
		<div class="form-group">
			<select class="form-control show-tick ms selectpicker" name="network" id="network" data-placeholder="Selecione">
				@if (!is_null($billing))
					<option value="Facebook" {{ $billing->network == 'facebook' ? 'selected="selected"' : '' }}>
						@lang('system.facebook')
					</option>
					<option value="Twitter" {{ $billing->network == 'twitter' ? 'selected="selected"' : '' }}>
						@lang('system.twitter')
					</option>
                    <option value="Whatsapp" {{ $billing->network == 'whatsapp' ? 'selected="selected"' : '' }}>
						@lang('system.whatsapp')
					</option>
				@else
                    <option value="Facebook" {{ old('network') == 'facebook' ? 'selected="selected"' : '' }}>
						@lang('system.facebook')
					</option>
					<option value="Twitter" {{ old('network') == 'twitter' ? 'selected="selected"' : '' }}>
						@lang('system.twitter')
					</option>
                    <option value="Whatsapp" {{ old('network') == 'whatsapp' ? 'selected="selected"' : '' }}>
						@lang('system.whatsapp')
					</option>
				@endif
			</select>
		</div>
	</div>
	<div class="col-sm-4" style="text-align: center">
		<label for="sessions">@lang('system.sessions')</label>
		<div class="form-group">
			<input class="form-control" type="sessions" id="sessions" placeholder="@lang('system.inform_the_m', ['value' => trans('system.sessions')])" value="{{ !is_null($billing) ? $billing->sessions : old('sessions') }}" name="sessions" />
		</div>
	</div>
    <div class="col-sm-4" style="text-align: center">
		<label for="price">@lang('system.price')</label>
		<div class="form-group">
			<input class="form-control" type="price" id="price" placeholder="@lang('system.inform_the_m', ['value' => trans('system.price')])" value="{{ !is_null($billing) ? $billing->price : old('price') }}" name="price" />
		</div>
	</div>
</div>
