<div class="table-responsive">
    <table id="general-table" class="table table-hover c_table"
        aria-describedby="@lang('system.create_m', ['value' => trans('system.general_table')])">
        <thead class="thead-dark">
            <tr>
                <td class="text-center">#</td>
                <td class="text-center">@lang('system.conversation_id')</td>
                <td class="text-left">@lang('system.terminate')</td>
                <td class="text-center">@lang('system.network')</td>
                <td class="text-center">@lang('system.created_at')</td>
                <td class="text-center">@lang('system.updated_at')</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $info)
                <tr>
                    <th class="text-center">{{ $info->id }}</th>
                    <th class="text-center">{{ $info->conversationId }}</th>
                    @if ($info->terminate)
                        <td class="text-left" style="color : #28a745; font-size : 20px"><i
                                class="fas fa-check-circle"></i><p style="display: none"> Sim</p></td>
                    @else
                        <td class="text-left" style="color : #f2282b; font-size : 20px"><i
                                class="fas fa-times-circle"></i></i><p style="display: none"> Não</p></td>
                    @endif
                    <td class="text-center">{{ $info->channel }}</td>
                    @if ($info->created_at)
                        <td class="text-center">{{ date('d/m/Y H:i:s', strtotime($info->created_at)) }}</td>
                    @else
                        <td class="text-center">-</td>
                    @endif
                    @if ($info->updated_at)
                        <td class="text-center">{{ date('d/m/Y H:i:s', strtotime($info->updated_at)) }}</td>
                    @else
                        <td class="text-center">-</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
