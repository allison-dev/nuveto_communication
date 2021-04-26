@extends('adminlte::page')
@include('pages.admin.invoices.index.breadcrumbs')
@section('content')
    <div class="card-header">
        <h2><b>@lang('system.create_m', ['value' => trans('system.invoice')])</b></h2>
    </div>
    @if ($invoices)
        @if ($invoices->company)
            <div class="card card-info">
                <form class="form-horizontal" action="" method="POST">
                    @csrf
                    <div class="card-body">
                        @include('pages.admin.invoices.form.inputs')
                    </div>
                    <div class="card-footer">
                        <button id="redirect" type="button" class="btn bg-teal btn-icon float-right text-white"
                            data-toggle="tooltip"
                            title="@lang('system.store_invoice', ['value' => trans('system.invoices')])">
                            <span>@lang('system.store_invoice', ['value' => trans('system.invoice')])</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="card card-info">
                <div class="card-body">
                    @include('pages.admin.invoices.form.company')
                </div>
                <div class="card-footer">
                </div>
            </div>
        @endif
    @else
        <div class="card card-info">
            <div class="card-body">
                @include('pages.admin.invoices.form.company')
            </div>
            <div class="card-footer">
            </div>
        </div>
    @endif
@endsection
@include('pages.admin.invoices.form.styles')
@include('pages.admin.invoices.form.scripts')
