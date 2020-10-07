@extends('adminlte::page')
@include('pages.admin.patients.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.patients')]) {{ $patients->id }}</b></h2>
	</div>
	<div class="card">
		<form action="{{ route('admin.patients.update', $patients->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $patients->id }}" />
			<div class="body">
				@include('pages.admin.patients.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.patients')])">
				<span>@lang('system.update', ['value' => trans('system.patients')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.patients.form.styles')
@include('pages.admin.patients.form.scripts')