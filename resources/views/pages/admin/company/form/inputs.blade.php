<div class="body">
	<ul class="nav nav-tabs p-0 mb-3">
		<li class="nav-item">
			<a class="nav-link active"
			   data-toggle="tab"
			   href="#general">
				@lang('system.general')
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link"
			   data-toggle="tab"
			   href="#addresses">
				@lang('system.address')
			</a>
		</li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel"
			 class="tab-pane in active"
			 id="general">
			@include('pages.admin.company.form.general')
		</div>
		<div role="tabpanel"
			 class="tab-pane"
			 id="addresses">
			@include('pages.admin.company.form.addresses')
		</div>
	</div>
</div>
