@extends('adminlte::page')

@section('content')
	<div class="card">
		<div class="header">
			<h2><strong><em class="fa fa-exclamation-circle"></em> @lang('system.403.title')</strong></h2>
		</div>
		<div class="body">
			<div class="alert alert-dismissible alert-danger text-center"
				 role="alert">
				<div class="container">
					<strong>@lang('system.403.msg')</strong>
					<button class="close"
							type="button"
							data-dismiss="alert"
							aria-label="Close">
						<span aria-hidden="true"><i class="fas fa-times"></i></span>
					</button>
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:window.history.back();"
				   class="btn btn-warning  text-white"
				   data-toggle="tooltip">
					@lang('system.back')
				</a>
			</div>
		</div>
	</div>
@endsection

