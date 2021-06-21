@extends('adminlte::page')

@section('title', 'Sigma')

@section('content_header')
    <h1 class="m-0 text-dark">Bem Vindo <b>{{ Auth::user()->name }}</b></h1>
@stop

@section('content')
    <div class="chart-container col-sm-12" style="position: relative;">
        {!! $line_chartjs->render() !!}
        {!! $bar_chartjs->render() !!}
        {!! $pie_chartjs->render() !!}
    </div>
@stop

<script>
    var delayed;

</script>
