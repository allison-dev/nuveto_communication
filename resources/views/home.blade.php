@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Painel</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Sigma Messenger
                    <p>
                        <a href="/sigma">Sigma Messenger</a>
                    </p>
                    search and contact someone to add it to your contacts list
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
