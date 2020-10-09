@extends('adminlte::page')
@include('pages.admin.patients.form.breadcrumbs')
@section('content')
<div class="card-header">
	<h2><b>@lang('system.create_m', ['value' => trans('system.patient')])</b></h2>
</div>
<div class="card card-info">
	<form class="form-horizontal" action="{{ route('admin.patients.store') }}" method="POST">
		@csrf
		<div class="card-body">
			@include('pages.admin.patients.form.inputs')
		</div>
		<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.store', ['value' => trans('system.patients')])">
				<span>@lang('system.store', ['value' => trans('system.patients')])</span>
			</button>
		</div>
	</form>
</div>
@endsection
@include('pages.admin.patients.form.styles')
@include('pages.admin.patients.form.scripts')