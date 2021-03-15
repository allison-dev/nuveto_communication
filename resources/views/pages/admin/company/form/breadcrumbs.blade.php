@section('breadcrumbs')
	<ol class="breadcrumb-item">
		<a href="{{ config('custom.url') }}" data-toggle="tooltip" title="" data-original-title="@lang('system.home')">
			<em class="zmdi zmdi-home zmdi-hc-lg"></em> @lang('system.home')
		</a>
	</ol>
	<ol class="breadcrumb-item">
		<a href="{{ route('admin.company.index') }}" data-toggle="tooltip" title="" data-original-title="@lang('system.companies')">
			@lang('system.companies')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@if (!is_null($company))
			@lang('system.edit', ['value' => trans('system.company')]) {{ $company->name }}
		@else
			@lang('system.create_m', ['value' => trans('system.company')])
		@endif
	</ol>
@endsection
