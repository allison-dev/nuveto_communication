@extends('adminlte::page')
@include('pages.admin.schedules.index.breadcrumbs')
@section('content_header')
<div class="header">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<a class="btn btn-success float-right text-white" href="{{ route('admin.schedules.create') }}" title="@lang('system.create_m', ['value' => trans('system.scheduling')])">
				<i class="fas fa-plus"></i>
			</a>
		</div>
	</div>
	<h2><strong>@lang('system.scheduling')</strong></h2>
</div>
@stop
@section('content')
	<div class="card project_list">
		@if ($schedules)
			@include('pages.admin.schedules.index.table')
		@else
			<div class="alert alert-dismissible alert-danger text-center"
				 role="alert">
				<div class="container">
					<strong>@lang('system.no_results_m', ['value' => trans('system.scheduling')])</strong>
					<button class="close" type="button" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true"><i class="fas fa-times"></i></span>
					</button>
				</div>
			</div>
		@endif
	</div>
@endsection
@include('pages.admin.schedules.form.styles')
@include('pages.admin.schedules.form.scripts')
