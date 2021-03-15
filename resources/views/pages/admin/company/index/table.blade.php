<div class="table-responsive">
	<table id="companies-table" class="table table-hover c_table"
		   aria-describedby="@lang('system.create_m', ['value' => trans('system.company')])">
		<thead class="thead-dark">
		<tr>
			<td class="text-center">#</td>
			<td class="text-left">@lang('system.name')</td>
			<td class="text-left">@lang('system.email')</td>
			<td class="text-center">@lang('system.cellphone')</td>
			<td class="text-center">@lang('system.created_at')</td>
			<td class="text-center">@lang('system.actions')</td>
		</tr>
		</thead>
		<tbody>
		@foreach($companies as $company)
			<tr>
				<th class="text-center">{{ $company->id }}</th>
				<td class="text-left">
					{{ $company->name }}
				</td>
				<td class="text-left">{{ $company->email }}</td>
				<td class="text-center">{{ $company->cellphone }}</td>
				<td class="text-center">{{ date('d/m/Y H:i:s', strtotime($company->created_at)) }}</td>
				<td class="text-center">
					<a href="{{ route('admin.company.edit', $company->id) }}"
					   class="btn btn-warning btn-icon text-white"
					   data-toggle="tooltip"
					   title="@lang('system.edit', ['value' => trans('system.company')])">
						<i class="far fa-edit"></i>
					</a>
					<a class="btn btn-danger text-white"
					   data-toggle="tooltip"
					   title="@lang('system.destroy', ['value' => trans('system.company')])"
					   onclick="swalDestroy('{{ $company->id }}', '@lang('system.destroy_cancel_m', ['value' => trans('system.company')])')">
						<i class="far fa-trash-alt"></i>
						<form style="display:none;"
							  action="{{ route('admin.company.destroy', $company->id) }}"
							  method="post"
							  id="form-destroy-{{ $company->id }}">
							@csrf
							<input name="_method"
								   type="hidden"
								   value="DELETE">
						</form>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
</div>
