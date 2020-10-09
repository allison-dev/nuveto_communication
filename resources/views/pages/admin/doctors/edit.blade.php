@extends('adminlte::page')
@include('pages.admin.doctors.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.doctors')]) {{ $doctor->id }}</b></h2>
	</div>
	<div class="card card-info">
		<form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $doctor->id }}" />
			<div class="card-body">
				@include('pages.admin.doctors.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.doctors')])">
				<span>@lang('system.update', ['value' => trans('system.doctors')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.doctors.form.styles')
@include('pages.admin.doctors.form.scripts')