@section('adminlte_js')
	<script src="{{ asset('custom/admin/plugins/select2/select2.min.js') }}"></script>
	<script src="{{ asset('custom/admin/plugins/multi-select/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('js/postcode.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.60/inputmask/jquery.inputmask.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/i18n/defaults-pt_BR.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.22.2/sweetalert2.all.min.js"></script>

	<script type="text/javascript">
        let urlPostcode = '{{ route('admin.address.showByPostcode') }}';
        let token = "{{ csrf_token() }}";
        $(function () {
            $('.select2').select2();
        });
        $('[data-mask]').inputmask();
        $('#cpf').inputmask({
            mask: [
                '999.999.999-99',
            ],
            keepStatic: true,
        });
        $('#cellphone').inputmask({
            mask: [
                '(99)9999-9999',
                '(99)99999-9999',
            ],
            keepStatic: true,
        });

        $('#days_week').selectpicker({
            'actionsBox' : true,
            'liveSearch' : true,
            'width'      : 'fit',
            'noneSelectedText' : 'Selecione os Dias da Semana',
            'style' : '',
            'styleBase' : 'form-control',
            'showTick' : true,
            'deselectAllText' : 'Desmarcar',
            'selectAllText' : 'Selecionar',
        });

        $('#patients-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json'
            }
        });

        // swalDestroy
        function swalDestroy (id, cancelSuccessText, title, text, formText = 'destroy')
        {
            let textTitle = false;
            let textText = false;

            if (title) {
                textTitle = title;
            }
            if (text) {
                textText = text;
            }
            swal({
                title: textTitle ? textTitle : 'Tem certeza disso?',
                text: textText ? textText : 'Esta ação pode ser irreverssível!',
                type: 'error',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                console.log(result.value);
                if (result.value) {
                    $('#form-' + formText + '-' + id).submit();
                } else {
                    swal({
                        title: cancelSuccessText ? cancelSuccessText : 'Médico não Removido',
                        type: 'error',
                        confirmButtonColor: '#5cb85c',
                    });
                }
            });
        }
	</script>
@endsection
