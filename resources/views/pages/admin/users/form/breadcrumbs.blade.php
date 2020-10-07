@section('breadcrumbs')
	<ol class="breadcrumb-item">
		<a href="{{ config('custom.url') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.home')">
			<em class="zmdi zmdi-home zmdi-hc-lg"></em> @lang('system.home')
		</a>
	</ol>
	<ol class="breadcrumb-item">
		<a href="{{ route('admin.users.index') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.users')">
			@lang('system.users')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if(!is_null($user))
			@lang('system.edit', ['value' => trans('system.user')]) {{ $user->name }}
		@else
			@lang('system.create_m', ['value' => trans('system.user')])
		@endif
	</ol>
@endsection