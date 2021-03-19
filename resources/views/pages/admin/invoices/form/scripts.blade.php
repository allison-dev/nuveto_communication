@section('adminlte_js')
	<script src="{{ asset('custom/admin/plugins/select2/select2.min.js') }}"></script>
	<script src="{{ asset('custom/admin/plugins/multi-select/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('js/postcode.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.60/inputmask/jquery.inputmask.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
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
                '99.999.999/9999-99'
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
            'actionsBox': true,
            'liveSearch': true,
            'width': 'fit',
            'noneSelectedText': 'Selecione os Dias da Semana',
            'style': '',
            'styleBase': 'form-control',
            'showTick': true,
            'deselectAllText': 'Desmarcar',
            'selectAllText': 'Selecionar',
        });

        $('#companies-table').DataTable({
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
        function swalDestroy(id, cancelSuccessText, title, text, formText = 'destroy') {
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

        $(document).on('click', '#redirect', function (e) {
            e.preventDefault();
            if($('#error_ini_date').length){
                $('#error_ini_date').remove();
            }

            if($('#error_end_date').length){
                $('#error_end_date').remove();
            }

            if(!empty($('#ini_date').val())){
                $('#ini_date').removeAttr('style');
            }

            if(!empty($('#end_date').val())) {
                $('#end_date').removeAttr('style');
            }
            let url = '{{ route('admin.invoice.generate') }}';

            if(empty($('#ini_date').val())) {
                $('#ini_date').attr({
                        style : 'border-color:#f2282b; color:#f2282b'
                    });
                animateCSS('#ini_date', 'tada').then((message) => {
                    $('#ini_date').after("<div id='error_ini_date' style='color:red;'>Por favor selecione uma Data Inicial Valida.</div>");
                    animateCSS('#error_ini_date', 'lightSpeedInLeft');
                });
            } else if (empty($('#end_date').val())) {
                $('#end_date').attr({
                        style : 'border-color:#f2282b; color:#f2282b'
                    });
                animateCSS('#end_date', 'tada').then((message) => {
                    $('#end_date').after("<div id='error_end_date' style='color:red;'>Por favor selecione uma Data Final Valida.</div>");
                    animateCSS('#error_end_date', 'lightSpeedInRight');
                });
            } else if (!empty($('#ini_date').val()) && !empty($('#end_date').val())) {
                url += '?ini_date=' + $('#ini_date').val() + '&end_date=' + $('#end_date').val();

                window.open(url, '_blank');
            }
        });

        function empty(e) {
            switch (e) {
                case "":
                case 0:
                case "0":
                case null:
                case false:
                case typeof (e) == "undefined":
                    return true;
                default:
                    return false;
            }
        }

        const animateCSS = animate();

        function animate() {
            return (element, animation, prefix = 'animate__') =>
                // We create a Promise and return it
                new Promise((resolve) => {
                    const animationName = `${prefix}${animation}`;
                    const node = document.querySelector(element);

                    node.classList.add(`${prefix}animated`, animationName);

                    // When the animation ends, we clean the classes and resolve the Promise
                    function handleAnimationEnd(event) {
                        event.stopPropagation();
                        node.classList.remove(`${prefix}animated`, animationName);
                        resolve('Animation ended');
                    }

                    node.addEventListener('animationend', handleAnimationEnd, { once: true });
                });
        }

	</script>
@endsection
