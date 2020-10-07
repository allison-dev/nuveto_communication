@extends('adminlte::page')
@include('pages.admin.schedules.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.scheduling')]) {{ $scheduling->id }}</b></h2>
	</div>
	<div class="card">
		<form action="{{ route('admin.schedules.update', $scheduling->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $scheduling->id }}" />
			<div class="body">
				@include('pages.admin.schedules.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.scheduling')])">
				<span>@lang('system.update', ['value' => trans('system.scheduling')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.schedules.form.styles')
@include('pages.admin.schedules.form.scripts')