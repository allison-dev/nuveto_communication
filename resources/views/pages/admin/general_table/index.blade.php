@extends('adminlte::page')
@include('pages.admin.general_table.index.breadcrumbs')
@section('content_header')
	<div class="header">
		<h2><strong>@lang('system.index', ['value' => trans('system.general_table')])</strong></h2>
	</div>
@stop
@section('content')
<div class="card project_list">
	@if ($data)
		@include('pages.admin.general_table.index.table')
	@else
	<div class="alert alert-dismissible alert-danger text-center" role="alert">
		<div class="container">
			<strong>@lang('system.no_results_m', ['value' => trans('system.general_table_empty')])</strong>
			<button class="close" type="button" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="fas fa-times"></i></span>
			</button>
		</div>
	</div>
	@endif
</div>
@endsection
@include('pages.admin.general_table.form.styles')
@include('pages.admin.general_table.form.scripts')
