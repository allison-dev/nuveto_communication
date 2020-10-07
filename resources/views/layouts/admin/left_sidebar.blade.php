<aside id="leftsidebar"
	   class="sidebar">
	<div class="navbar-brand">
		<button class="btn-menu ls-toggle-btn"
				type="button">
			<em class="zmdi zmdi-menu"></em>
		</button>
		<a href="{{ config('custom.url_panel') }}">
			<span class="m-l-10">{{ config('custom.name') }}</span>
		</a>
	</div>
	<div class="menu">
		<ul class="list">
			<li>
				<div class="user-info">
					<a class="image"
					   href="{{ config('custom.url_panel') }}">
					</a>
					<div class="detail">
						<small>{{ auth()->user()->name }}</small>
					</div>
				</div>
			</li>
			<li class="{{ menuActive(['']) }}">
				<a href="{{ config('custom.url_panel') }}">
					<em class="zmdi zmdi-home"></em>
					<span>@lang('system.dashboard')</span>
				</a>
			</li>
			<li class="{{ menuActive(['agendamentos']) }}">
				<a href="{{ route('admin.schedules.index') }}">
					<em class="zmdi zmdi-assignment-o"></em>
					<span>@lang('system.scheduling')</span>
				</a>
			</li>
			<li class="{{ menuActive(['pacientes']) }}">
				<a href="{{ route('admin.patients.index') }}">
					<em class="zmdi zmdi-account"></em>
					<span>@lang('system.patients')</span>
				</a>
			</li>
			<li class="{{ menuActive(['medicos']) }}">
				<a href="{{ route('admin.doctors.index') }}">
					<em class="zmdi zmdi-male-alt"></em>
					<span>@lang('system.doctors')</span>
				</a>
			</li>
			<li class="{{ menuActive(['usuarios']) }}">
				<a href="javascript:void(0);"
				   class="menu-toggle">
					<em class="zmdi zmdi-lock"></em>
					<span>@lang('system.authentication')</span>
				</a>
				<ul class="ml-menu">
					<li class="{{ menuActive(['usuarios']) }}">
						<a href="{{ route('admin.users.index') }}">
							<p>@lang('system.users')</p>
						</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="{{ route('admin.logout') }}">
					<em class="zmdi zmdi-power"></em>
					<span>@lang('system.logout')</span>
				</a>
			</li>
		</ul>
	</div>
</aside>
