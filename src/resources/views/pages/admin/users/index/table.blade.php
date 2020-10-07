<div class="table-responsive">
	<table id="user-table" class="table table-hover c_table dataTable" aria-describedby="@lang('system.create_m', ['value' => trans('system.user')])">
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
		@foreach($users as $user)
			<tr>
				<th class="text-center" id="{{ $user->id }}">{{ $user->id }}</th>
				<td class="text-left">{{ $user->name }}</td>
				<td class="text-left">{{ $user->email }}</td>
				<td class="text-center">{{ date('d/m/Y H:i:s', strtotime($user->created_at)) }}</td>
				<td class="text-center">
					<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-icon text-white" data-toggle="tooltip" title="@lang('system.edit', ['value' => trans('system.user')])">
						<i class="far fa-edit"></i>
					</a>
					<a class="btn btn-danger text-white" data-toggle="tooltip" title="@lang('system.destroy', ['value' => trans('system.user')])" onclick="swalDestroy('{{ $user->id }}', '@lang('system.destroy_cancel_m', ['value' => trans('system.user')])')">
						<i class="far fa-trash-alt"></i>
						<form style="display:none;" action="{{ route('admin.users.destroy', $user->id) }}" method="post" id="form-destroy-{{ $user->id }}">
							@csrf
							<input name="_method" type="hidden" value="DELETE">
						</form>
					</a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
</div>