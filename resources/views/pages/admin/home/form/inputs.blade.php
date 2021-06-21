<div class="row clearfix">
	<div class="card-body">
		<label for="name">@lang('system.name')</label>
		<div class="form-group">
			<input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_m', ['value' => trans('system.name')])" id="name" value="{{ !is_null($user) ? $user->name : old('name') }}" name="name" />
			@if ($errors->has('name'))
				<label id="name-error" class="error" for="name"><strong>{{ $errors->first('name') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="card-body">
		<label for="email">@lang('system.email')</label>
		<div class="form-group">
			<input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_m', ['value' => trans('system.email')])" id="email" value="{{ !is_null($user) ? $user->email : old('email') }}" name="email" />
			@if ($errors->has('email'))
				<label id="email-error" class="error" for="email"><strong>{{ $errors->first('email') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="card-body">
		<label for="password">@lang('system.password')</label>
		<div class="form-group">
			<input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_f', ['value' => trans('system.password')])" id="password" name="password" value="{{ old('password') }}" />
			@if ($errors->has('password'))
				<label id="password-error" class="error" for="password"><strong>{{ $errors->first('password') }}</strong></label>
			@endif
		</div>
	</div>
	<div class="card-body">
		<label for="password_confirmation">@lang('system.password_confirmation')</label>
		<div class="form-group">
			<input type="password" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" placeholder="@lang('system.inform_the_f', ['value' => trans('system.password_confirmation')])" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" />
			@if ($errors->has('password_confirmation'))
				<label id="password_confirmation-error" class="error" for="password_confirmation"><strong>{{ $errors->first('password_confirmation') }}</strong></label>
			@endif
		</div>
	</div>
</div>