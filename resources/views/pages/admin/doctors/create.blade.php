@extends('adminlte::page')
@include('pages.admin.doctors.form.breadcrumbs')
@section('content')
<div class="card-header">
	<h2><b>@lang('system.create_m', ['value' => trans('system.doctor')])</b></h2>
</div>
<div class="card card-info">
	<form class="form-horizontal" action="{{ route('admin.doctors.store') }}" method="POST">
		@csrf
		<div class="card-body">
			@include('pages.admin.doctors.form.inputs')
		</div>
		<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.store', ['value' => trans('system.scheduling')])">
				<span>@lang('system.store', ['value' => trans('system.scheduling')])</span>
			</button>
		</div>
	</form>
</div>
@endsection
@include('pages.admin.doctors.form.styles')
@include('pages.admin.doctors.form.scripts')