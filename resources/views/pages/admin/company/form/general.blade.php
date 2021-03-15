<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<label for="name">@lang('system.name')</label>
		<div class="form-group">
			<input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" id="name" placeholder="@lang('system.inform_the_m', ['value' => trans('system.name')])" value="{{ !is_null($company) ? $company->name : old('name') }}" name="name" />
			@if ($errors->has('name'))
				<label id="name-error" class="error" for="name"><strong>{{ $errors->first('name') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12">
		<label for="email">@lang('system.email')</label>
		<div class="form-group">
			<input class="form-control" type="email" id="email" placeholder="@lang('system.inform_the_m', ['value' => trans('system.email')])" value="{{ !is_null($company) ? $company->email : old('email') }}" name="email" />
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12">
		<label for="cpf_cnpj">@lang('system.cpf')</label>
		<div class="form-group">
			<input class="form-control {{ $errors->has('cpf_cnpj') ? 'is-invalid' : '' }}"
				   type="text" id="cpf_cnpj" placeholder="@lang('system.inform_the_m', ['value' => trans('system.cpf')])" value="{{ !is_null($company) ? $company->cpf_cnpj : old('cpf_cnpj') }}" name="cpf_cnpj" />
			@if ($errors->has('cpf_cnpj'))
				<label id="cpf_cnpj-error" class="error" for="cpf_cnpj"><strong>{{ $errors->first('cpf_cnpj') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12">
		<label for="birthday">@lang('system.birth')</label>
		<div class="form-group">
			<input class="form-control {{ $errors->has('birthday') ? 'is-invalid' : '' }}" type="date" id="birthday" placeholder="@lang('system.inform_the_m', ['value' => trans('system.birthday')])" value="{{ !is_null($company) ? $company->birthday : old('birthday') }}" name="birthday" />
			@if ($errors->has('birthday'))
				<label id="birthday-error" class="error" for="birthday"><strong>{{ $errors->first('birthday') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12">
		<div class="form-group">
			<label for="cellphone">@lang('system.cellphone')</label>
			<input class="form-control {{ $errors->has('cellphone') ? 'is-invalid' : '' }}" type="tel" id="cellphone" placeholder="@lang('system.inform_the_m', ['value' => trans('system.cellphone')])" value="{{ !is_null($company) ? $company->cellphone : old('cellphone') }}" name="cellphone" />
			@if ($errors->has('cellphone'))
				<label id="cellphone-error" class="error" for="cellphone"><strong>{{ $errors->first('cellphone') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-12">
		<label for="sex">@lang('system.sex')</label>
		<div class="form-group">
			<select class="form-control show-tick ms selectpicker" name="sex" id="sex" data-placeholder="Selecione">
				@if (!is_null($company))
					<option value="M" {{ $company->sex == 'M' ? 'selected="selected"' : '' }}>
						@lang('system.male')
					</option>
					<option value="F" {{ $company->sex == 'F' ? 'selected="selected"' : '' }}>
						@lang('system.female')
					</option>
				@else
					<option value="M" {{ old('sex') == 'M' ? 'selected="selected"' : '' }}>
						@lang('system.male')
					</option>
					<option value="F" {{ old('sex') == 'F' ? 'selected="selected"' : '' }}>
						@lang('system.female')
					</option>
				@endif
			</select>
		</div>
	</div>
</div>
