@extends('adminlte::page')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.admin.head')
    @yield('extra-styles')
    <link rel="stylesheet"
          href="{{ asset('css/app.css') }}">
</head>

<body class="theme-green ">
@include('layouts.admin.loader')
<div class="overlay"></div>
<section class="content">
    <div class="body_scroll">
        @yield('content')
    </div>
</section>
@include('layouts.admin.scripts')
@yield('extra-scripts')
</body>
</html>
