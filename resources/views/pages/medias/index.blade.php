@extends('adminlte::page')
@include('pages.medias.index.breadcrumbs')
@section('content')
    <div class="card-header">
        <h2><b>@lang('system.create_m', ['value' => trans('system.medias')])</b></h2>
    </div>
    @if ($medias)
        <div class="card card-info">
            <form class="form-horizontal" action="" method="POST">
                @csrf
                <div class="card-body">
                    @include('pages.medias.form.inputs')
                </div>
            </form>
        </div>
    @else
        <div class="card card-info">
            <div class="card-body">
                @include('pages.medias.form.nomedia')
            </div>
            <div class="card-footer">
            </div>
        </div>
    @endif
@endsection
@include('pages.medias.form.styles')
@include('pages.medias.form.scripts')
