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
		<a href="{{ route('admin.billings.index') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.billing')">
			@lang('system.billing')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if (!is_null($billing))
			@lang('system.edit', ['value' => trans('system.billing')]) {{ $billing->name }}
		@else
			@lang('system.create_m', ['value' => trans('system.billing')])
		@endif
	</ol>
@endsection
