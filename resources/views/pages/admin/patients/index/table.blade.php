<div class="table-responsive">
	<table id="patients-table" class="table table-hover c_table"
		   aria-describedby="@lang('system.create_m', ['value' => trans('system.patient')])">
		<thead class="thead-dark">
		<tr>
			<td class="text-center">#</td>
			<td class="text-left">@lang('system.name')</td>
			<td class="text-left">@lang('system.email')</td>
			<td class="text-center">@lang('system.created_at')</td>
			<td class="text-center">@lang('system.actions')</td>
		</tr>
		</thead>
		<tbody>
		@foreach($patients as $patient)
			<tr>
				<th class="text-center">{{ $patient->id }}</th>
				<td class="text-left">
					{{ $patient->name }}
				</td>
				<td class="text-left">{{ $patient->email }}</td>
				<td class="text-center">{{ date('d/m/Y H:i:s', strtotime($patient->created_at)) }}</td>
				<td class="text-center">
					<a href="{{ route('admin.patients.edit', $patient->id) }}"
					   class="btn btn-warning btn-icon text-white"
					   data-toggle="tooltip"
					   title="@lang('system.edit', ['value' => trans('system.patient')])">
						<i class="far fa-edit"></i>
					</a>
					<a class="btn btn-danger text-white"
					   data-toggle="tooltip"
					   title="@lang('system.destroy', ['value' => trans('system.patient')])"
					   onclick="swalDestroy('{{ $patient->id }}', '@lang('system.destroy_cancel_m', ['value' => trans('system.patient')])')">
						<i class="far fa-trash-alt"></i>
						<form style="display:none;"
							  action="{{ route('admin.patients.destroy', $patient->id) }}"
							  method="post"
							  id="form-destroy-{{ $patient->id }}">
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