@extends('adminlte::page')
@include('pages.admin.users.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.user')]) {{ $user->id }}</b></h2>
	</div>
	<div class="card">
		<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $user->id }}" />
			<div class="body">
				@include('pages.admin.users.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.user')])">
				<span>@lang('system.update', ['value' => trans('system.user')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.users.form.styles')
@include('pages.admin.users.form.scripts')