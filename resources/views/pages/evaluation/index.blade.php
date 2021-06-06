@extends('adminlte::page')
@include('pages.evaluation.index.breadcrumbs')
@section('content')
    <div class="card-header">
        <h2><b>@lang('system.create_f', ['value' => trans('system.evaluation')])</b></h2>
        <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <h4>Condições</h4>
            <ul>
                <li>O pedido será enviado para o e-mail de registro do consumidor</li>
                <li>Para solicitar, a última interação pública deve ser da empresa</li>
                <li>Última interação pública exigida há mais de 3 dias</li>
                <li>O Ticket não deve ser avaliado</li>
            </ul>
        </div>
    </div>
    <div class="card card-info">
        @if (isset($evaluation['message']))
            <div class="card-body">
                @include('pages.evaluation.evaluationsend')
            </div>
        @else
            <form class="form-horizontal" action="{{ route('reclame_aqui.evaluation') }}" method="POST">
                @csrf
                <div class="card-body">
                    @include('pages.evaluation.form.inputs')
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-teal btn-icon float-right text-white" data-toggle="tooltip"
                        title="@lang('system.send', ['value' => trans('system.evaluation')])">
                        <span>@lang('system.send', ['value' => trans('system.evaluation')])</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection
@include('pages.evaluation.form.styles')
@include('pages.evaluation.form.scripts')
