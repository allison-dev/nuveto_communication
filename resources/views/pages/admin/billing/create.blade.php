@extends('adminlte::page')
@include('pages.admin.billing.form.breadcrumbs')
@section('content')
<div class="card-header">
	<h2><b>@lang('system.create_m', ['value' => trans('system.billing')])</b></h2>
</div>
<div class="card card-info">
	<form class="form-horizontal" action="{{ route('admin.billings.store') }}" method="POST">
		@csrf
		<div class="card-body">
			@include('pages.admin.billing.form.inputs')
		</div>
		<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.store', ['value' => trans('system.billing')])">
				<span>@lang('system.store', ['value' => trans('system.billing')])</span>
			</button>
		</div>
	</form>
</div>
@endsection
@include('pages.admin.billing.form.styles')
@include('pages.admin.billing.form.scripts')
