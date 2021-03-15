@extends('adminlte::page')
@include('pages.admin.billing.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.billing')]) {{ $billing->id }}</b></h2>
	</div>
	<div class="card">
		<form action="{{ route('admin.billings.update', $billing->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $billing->id }}" />
			<div class="body">
				@include('pages.admin.billing.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.billing')])">
				<span>@lang('system.update', ['value' => trans('system.billing')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.billing.form.styles')
@include('pages.admin.billing.form.scripts')
