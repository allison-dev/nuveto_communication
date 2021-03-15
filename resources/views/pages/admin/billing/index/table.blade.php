<div class="table-responsive">
	<table id="patients-table" class="table table-hover c_table"
		   aria-describedby="@lang('system.create_m', ['value' => trans('system.billing')])">
		<thead class="thead-dark">
		<tr>
			<td class="text-center">#</td>
			<td class="text-center">@lang('system.network')</td>
			<td class="text-center">@lang('system.sessions')</td>
			<td class="text-left">@lang('system.price')</td>
			<td class="text-right">@lang('system.created_at')</td>
			<td class="text-right">@lang('system.actions')</td>
		</tr>
		</thead>
		<tbody>
		@foreach($billings as $billing)
			<tr>
				<th class="text-center">{{ $billing->id }}</th>
				<td class="text-center">
					{{ $billing->network }}
				</td>
				<td class="text-center">{{ $billing->sessions }}</td>
				<td class="text-left">R${{ str_replace('.', ',', $billing->price) }}</td>
				<td class="text-right">{{ date('d/m/Y H:i:s', strtotime($billing->created_at)) }}</td>
				<td class="text-right">
					<a href="{{ route('admin.billings.edit', $billing->id) }}"
					   class="btn btn-warning btn-icon text-white"
					   data-toggle="tooltip"
					   title="@lang('system.edit', ['value' => trans('system.billing')])">
						<i class="far fa-edit"></i>
					</a>
					<a class="btn btn-danger text-white"
					   data-toggle="tooltip"
					   title="@lang('system.destroy', ['value' => trans('system.billing')])"
					   onclick="swalDestroy('{{ $billing->id }}', '@lang('system.destroy_cancel_m', ['value' => trans('system.billing')])')">
						<i class="far fa-trash-alt"></i>
						<form style="display:none;"
							  action="{{ route('admin.billings.destroy', $billing->id) }}"
							  method="post"
							  id="form-destroy-{{ $billing->id }}">
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
