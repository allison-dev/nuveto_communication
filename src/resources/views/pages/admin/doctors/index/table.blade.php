<div class="table-responsive">
	<table id="doctors-table" class="table table-hover c_table"
		   aria-describedby="@lang('system.create_m', ['value' => trans('system.doctor')])">
		<thead class="thead-dark">
		<tr>
			<td class="text-center">#</td>
			<td class="text-left">@lang('system.name')</td>
			<td class="text-left">@lang('system.email')</td>
			<td class="text-center">@lang('system.crm')</td>
			<td class="text-center">@lang('system.created_at')</td>
			<td class="text-center">@lang('system.actions')</td>
		</tr>
		</thead>
		<tbody>
		@foreach($doctors as $doctor)
			<tr>
				<th class="text-center">{{ $doctor->id }}</th>
				<td class="text-left">
					{{ $doctor->name }}
				</td>
				<td class="text-left">{{ $doctor->email }}</td>
				<td class="text-center">{{ $doctor->crm }}</td>
				<td class="text-center">{{ date('d/m/Y H:i:s', strtotime($doctor->created_at)) }}</td>
				<td class="text-center">
					<a href="{{ route('admin.doctors.edit', $doctor->id) }}"
					   class="btn btn-warning btn-icon text-white"
					   data-toggle="tooltip"
					   title="@lang('system.edit', ['value' => trans('system.doctor')])">
						<i class="far fa-edit"></i>
					</a>
					<a class="btn btn-danger text-white"
					   data-toggle="tooltip"
					   title="@lang('system.destroy', ['value' => trans('system.doctor')])"
					   onclick="swalDestroy('{{ $doctor->id }}', '@lang('system.destroy_cancel_m', ['value' => trans('system.doctor')])')">
						<i class="far fa-trash-alt"></i>
						<form style="display:none;"
							  action="{{ route('admin.doctors.destroy', $doctor->id) }}"
							  method="post"
							  id="form-destroy-{{ $doctor->id }}">
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