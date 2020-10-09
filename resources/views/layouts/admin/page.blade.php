@extends('layouts.admin.master')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12">
                @yield('page-title')
                <ul class="breadcrumb">
                    @yield('breadcrumbs')
                </ul>
                <button class="btn btn-dark btn-icon mobile_menu"
                        type="button">
                    <em class="zmdi zmdi-sort-amount-desc"></em></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                @include('flash::message')
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                @yield('content-page')
            </div>
        </div>
    </div>
@endsection
