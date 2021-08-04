@extends('adminlte::page')

@section('title', 'Sigma')

@section('content_header')
    <h1 class="m-0 text-dark">Bem Vindo <b>{{ Auth::user()->name }}</b></h1>
@stop

@section('content')
    <div class="row">
        @if ($line_chartjs)
            <div class="col-lg-6 col-sm-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title"><b>{{ $chart_titles['line'] }}</b></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="position-relative">
                                {!! $line_chartjs->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if ($pie_chartjs)
            <div class="col-lg-6 col-sm-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title"><b>{{ $chart_titles['pie'] }}</b></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! $pie_chartjs->render() !!}
                    </div>
                </div>
            </div>
        @endif
        @if ($concurrent_chartjs)
            <div class="col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title"><b>{{ $chart_titles['concurrent'] }}</b></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="position-relative">
                                {!! $concurrent_chartjs->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if ($bar_chartjs)
            <div class="col-lg-12 col-sm-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title"><b>{{ $chart_titles['bar'] }}</b></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="position-relative">
                                {!! $bar_chartjs->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@include('pages.admin.users.form.styles')
@include('pages.admin.users.form.scripts')
