@extends('adminlte::page')
@include('pages.moderation.index.breadcrumbs')
@section('content')
    <div class="card-header">
        <h2><b>@lang('system.create_f', ['value' => trans('system.moderation')])</b></h2>
    </div>
    <div class="card card-info">
        @if (isset($moderation['message']))
            <div class="card-body">
                @include('pages.moderation.moderationsend')
            </div>
        @else
            <form class="form-horizontal" action="{{ route('reclame_aqui.moderation') }}" method="POST">
                @csrf
                <div class="card-body">
                    @include('pages.moderation.form.inputs')
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip"
                        title="@lang('system.send', ['value' => trans('system.moderation')])">
                        <span>@lang('system.send', ['value' => trans('system.moderation')])</span>
                    </button>
                </div>
            </form>

        @endif
    </div>
@endsection
@include('pages.moderation.form.styles')
@include('pages.moderation.form.scripts')
