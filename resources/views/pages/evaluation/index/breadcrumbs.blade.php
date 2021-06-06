@section('breadcrumbs')
	<ol class="breadcrumb-item">
		<a href="{{ config('custom.url') }}"
		   data-toggle="tooltip"
		   title=""
		   data-original-title="@lang('system.home')">
			<em class="zmdi zmdi-home zmdi-hc-lg"></em> @lang('system.home')
		</a>
	</ol>
	<ol class="breadcrumb-item active">
		@lang('system.evaluation')
	</ol>
@endsection
