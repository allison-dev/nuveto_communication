@extends('adminlte::page')
@include('pages.admin.users.form.breadcrumbs')
@section('content')
<div class="card-header">
	<h2><b>@lang('system.create_m', ['value' => trans('system.scheduling')])</b></h2>
</div>
<div class="card">
	<form action="{{ route('admin.users.store') }}" method="POST">
		@csrf
		<div class="body">
			@include('pages.admin.users.form.inputs')
		</div>
		<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.store', ['value' => trans('system.scheduling')])">
				<span>@lang('system.store', ['value' => trans('system.scheduling')])</span>
			</button>
		</div>
	</form>
</div>
@endsection
@include('pages.admin.users.form.styles')
@include('pages.admin.users.form.scripts')