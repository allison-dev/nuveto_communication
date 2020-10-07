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
		<a href="{{ route('admin.schedules.index') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.scheduling')">
			@lang('system.scheduling')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if(!is_null($scheduling))
			@lang('system.edit', ['value' => trans('system.scheduling')]) {{ $scheduling->id }}
		@else
			@lang('system.create_m', ['value' => trans('system.scheduling')])
		@endif
	</ol>
@endsection