@extends('adminlte::page')
@include('pages.admin.company.form.breadcrumbs')
@section('content')
	<div class="card-header">
		<h2><b>@lang('system.edit', ['value' => trans('system.companies')]) {{ $company->id }}</b></h2>
	</div>
	<div class="card card-info">
		<form action="{{ route('admin.company.update', $company->id) }}" method="POST">
			@csrf
			<input name="_method" type="hidden" value="PUT">
			<input type="hidden" name="id" value="{{ $company->id }}" />
			<div class="card-body">
				@include('pages.admin.company.form.inputs')
			</div>
			<div class="card-footer">
			<button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip" title="@lang('system.update', ['value' => trans('system.companies')])">
				<span>@lang('system.update', ['value' => trans('system.companies')])</span>
			</button>
		</div>
		</form>
	</div>
@endsection
@include('pages.admin.company.form.styles')
@include('pages.admin.company.form.scripts')
