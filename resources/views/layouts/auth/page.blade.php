@extends('layouts.auth.master')

@section('page-login')
    <div class="authentication">
        <div class="container">
            <div class="row">
                @yield('content-login')
            </div>
        </div>
    </div>
@endsection
