@section('adminlte_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.22.2/sweetalert2.all.min.js"></script>
<script>
    $('#user-table').DataTable({
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