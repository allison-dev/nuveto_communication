@section('breadcrumbs')
	<ol class="breadcrumb-item">
		<a href="{{ config('custom.url') }}" data-toggle="tooltip" title="" data-original-title="@lang('system.home')">
			<em class="zmdi zmdi-home zmdi-hc-lg"></em> @lang('system.home')
		</a>
	</ol>
	<ol class="breadcrumb-item">
		<a href="{{ route('admin.doctors.index') }}" data-toggle="tooltip" title="" data-original-title="@lang('system.doctors')">
			@lang('system.doctors')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if (!is_null($doctor))
			@lang('system.edit', ['value' => trans('system.doctor')]) {{ $doctor->name }}
		@else
			@lang('system.create_m', ['value' => trans('system.doctor')])
		@endif
	</ol>
@endsection