<div class="row clearfix">
	<div class="col-lg-6 col-md-6 col-sm-12">
		<label for="postcode">@lang('system.postcode')</label>
		<div class="form-group">
			<input class="form-control {{ $errors->has('postcode') ? 'is-invalid' : '' }}" type="tel" id="postcode" placeholder="@lang('system.inform_the_m', ['value' => trans('system.postcode')])" value="{{ !is_null($doctor) && !is_null($doctor->address) ? $doctor->address->postcode : old('postcode') }}" name="postcode" data-inputmask='"mask": "99999-999"' data-mask />
			@if ($errors->has('postcode'))
				<label id="postcode-error" class="error" for="postcode"><strong>{{ $errors->first('postcode') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="street">@lang('system.address')</label>
		<div class="form-group">
			<input class="form-control" type="text" id="street" placeholder="@lang('system.inform_the_m', ['value' => trans('system.address')])" value="{{ !is_null($doctor) && !is_null($doctor->address) ? $doctor->address->street : old('street') }}" name="street" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="number_home">@lang('system.number_home')</label>
		<div class="form-group">
			<input class="form-control" type="tel" id="number_home" placeholder="@lang('system.inform_the_m', ['value' => trans('system.number_home')])" value="{{ !is_null($doctor) ? $doctor->number_home : old('number_home') }}" name="number_home" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="complement">@lang('system.complement')</label>
		<div class="form-group">
			<input class="form-control" type="text" id="complement" placeholder="@lang('system.inform_the_m', ['value' => trans('system.complement')])" value="{{ !is_null($doctor) ? $doctor->complement : old('complement') }}" name="complement" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="neighborhood">@lang('system.neighborhood')</label>
		<div class="form-group">
			<input class="form-control" type="text" id="neighborhood" placeholder="@lang('system.inform_the_m', ['value' => trans('system.neighborhood')])" value="{{ !is_null($doctor) && !is_null($doctor->address) ? $doctor->address->neighborhood : old('neighborhood') }}" name="neighborhood" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="city">@lang('system.city')</label>
		<div class="form-group">
			<input class="form-control" type="text" id="city" placeholder="@lang('system.inform_the_f', ['value' => trans('system.city')])" value="{{ !is_null($doctor) && !is_null($doctor->address) ? $doctor->address->city : old('city') }}" name="city" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 open_address">
		<label for="uf">@lang('system.uf')</label>
		<div class="form-group">
			<input class="form-control" type="text" id="uf" placeholder="@lang('system.inform_the_f', ['value' => trans('system.uf')])" value="{{ !is_null($doctor) && !is_null($doctor->address) ? $doctor->address->uf : old('uf') }}" name="uf" />
		</div>
	</div>
</div>
