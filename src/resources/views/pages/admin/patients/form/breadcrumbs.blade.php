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
		<a href="{{ route('admin.patients.index') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.patients')">
			@lang('system.patients')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if (!is_null($patient))
			@lang('system.edit', ['value' => trans('system.patient')]) {{ $patient->name }}
		@else
			@lang('system.create_m', ['value' => trans('system.patient')])
		@endif
	</ol>
@endsection