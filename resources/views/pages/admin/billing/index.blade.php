@extends('adminlte::page')
@include('pages.admin.billing.index.breadcrumbs')
@section('content_header')
	<div class="header">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<a class="btn btn-success float-right text-white" href="{{ route('admin.billings.create') }}" title="@lang('system.create_m', ['value' => trans('system.billing')])">
					<i class="fas fa-plus"></i>
				</a>
			</div>
		</div>
		<h2><strong>@lang('system.index', ['value' => trans('system.billing')])</strong></h2>
	</div>
@stop
@section('content')
<div class="card project_list">
	@if ($billings)
	@include('pages.admin.billing.index.table')
	@else
	<div class="alert alert-dismissible alert-danger text-center" role="alert">
		<div class="container">
			<strong>@lang('system.no_results_m', ['value' => trans('system.billing')])</strong>
			<button class="close" type="button" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="fas fa-times"></i></span>
			</button>
		</div>
	</div>
	@endif
</div>
@endsection
@include('pages.admin.billing.form.styles')
@include('pages.admin.billing.form.scripts')
