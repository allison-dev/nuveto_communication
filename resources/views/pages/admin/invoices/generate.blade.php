<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.0/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
<head>
    <title>
        Fatura - Sigma
    </title>
</head>

<body>
    <div id="invoice">
        <div class="toolbar hidden-print">
            <div class="text-right">
                <button id="printInvoice" class="btn btn-info"><i class="fa fa-print"></i> Imprimir</button>
                <button id="export_to_pdf" class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Exportar como
                    PDF</button>
            </div>
            <hr>
        </div>
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col" style="transform: translate3d(0px, -28px, 0px);">
                            <a target="_blank" href="https://nuveto.com.br">
                                <img src="{{ asset(config('adminlte.logo_img_invoice')) }}">
                            </a>
                        </div>
                        <div class="col company-details">
                            <div>Avenida Roque Petroni Júnior, 850, Jardim das Acacias, São Paulo - SP, 04707-000</div>
                            <div>(11) 4200-8282</div>
                            <div>marketing@nuveto.com.br</div>
                        </div>
                    </div>
                </header>
                <main>
                    <div class="row contacts">
                        <div class="col invoice-to">
                            <div class="text-gray-light">Fatura PARA:</div>
                            <h2 class="to">{{$invoices->name}}</h2>
                            <div class="address">{{$invoices->street}}, {{$invoices->number_home}}, {{$invoices->neighborhood}}, {{$invoices->city}} - {{$invoices->uf}},{{$invoices->postcode}}</div>
                            <div class="email"><a href="mailto:{{$invoices->email}}">{{$invoices->email}}</a>
                            </div>
                        </div>
                        <div class="col invoice-details">
                            <h1 class="invoice-id">ID : {{$invoices->invoice_id}}</h1>
                            <div class="date">Data de Inicio: {{$invoices->date_ini}}</div>
                            <div class="date">Data Final: {{$invoices->date_end}}</div>
                        </div>
                    </div>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-left">DESCRIÇÃO</th>
                                <th class="text-right">VALOR POR SESSÃO</th>
                                <th class="text-right">TOTAL DE SESSÕES</th>
                                <th class="text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($invoices->facebook_sessions)
                                <tr>
                                    <td class="no">01</td>
                                    <td class="text-left">
                                        <h3>Sessões Simultaneas - Facebook</h3>Quantidade de sessões utilizada no periodo
                                        através do Facebook
                                    </td>
                                    <td class="unit">R${{$invoices->get_billing_facebook->price}}</td>
                                    <td class="qty">{{$invoices->facebook_sessions}}</td>
                                    <td class="total">R${{$invoices->total->facebook}}</td>
                                </tr>
                            @endif
                            @if ($invoices->twitter_sessions)
                                <tr>
                                    <td class="no">02</td>
                                    <td class="text-left">
                                        <h3>Sessões Simultaneas - Twitter</h3>Quantidade de sessões utilizada no periodo
                                        através do Twitter
                                    </td>
                                    <td class="unit">R${{$invoices->get_billing_twitter->price}}</td>
                                    <td class="qty">{{$invoices->twitter_sessions}}</td>
                                    <td class="total">R${{$invoices->total->twitter}}</td>
                                </tr>
                            @endif
                            @if ($invoices->whatsapp_sessions)
                                <tr>
                                    <td class="no">03</td>
                                    <td class="text-left">
                                        <h3>Sessões Simultaneas - WhatsApp</h3>Quantidade de sessões utilizada no periodo
                                        através do WhatsApp
                                    </td>
                                    <td class="unit">R${{$invoices->get_billing_whatsapp->price}}</td>
                                    <td class="qty">{{$invoices->whatsapp_sessions}}</td>
                                    <td class="total">R${{$invoices->total->whatsapp}}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">SUBTOTAL</td>
                                <td>R${{$invoices->subtotal}}</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td colspan="2">TOTAL</td>
                                <td>R${{$invoices->full_total}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </main>
                <footer>
                    A fatura foi criada em um computador e é válida sem a assinatura e o selo.
                </footer>
            </div>
            <div></div>
        </div>
    </div>
</body>
<style>
    #invoice {
        padding: 30px;
    }

    .invoice {
        position        : relative;
        background-color: #FFF;
        min-height      : 680px;
        padding         : 15px
    }

    .invoice header {
        padding      : 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #3989c6
    }

    .invoice .company-details {
        text-align: right;
        display   : inline-block;
        align-self: center;
    }

    .invoice .company-details .name {
        margin-top   : 0;
        margin-bottom: 0
    }

    .invoice .contacts {
        margin-bottom: 20px
    }

    .invoice .invoice-to {
        text-align: left
    }

    .invoice .invoice-to .to {
        margin-top   : 0;
        margin-bottom: 0
    }

    .invoice .invoice-details {
        text-align: right
    }

    .invoice .invoice-details .invoice-id {
        margin-top: 0;
        color     : #3989c6
    }

    .invoice main {
        padding-bottom: 50px
    }

    .invoice main .thanks {
        margin-top   : -100px;
        font-size    : 2em;
        margin-bottom: 50px
    }

    .invoice main .notices {
        padding-left: 6px;
        border-left : 6px solid #3989c6
    }

    .invoice main .notices .notice {
        font-size: 1.2em
    }

    .invoice table {
        width          : 100%;
        border-collapse: collapse;
        border-spacing : 0;
        margin-bottom  : 20px
    }

    .invoice table td,
    .invoice table th {
        padding      : 15px;
        background   : #eee;
        border-bottom: 1px solid #fff
    }

    .invoice table th {
        white-space: nowrap;
        font-weight: 400;
        font-size  : 16px
    }

    .invoice table td h3 {
        margin     : 0;
        font-weight: 400;
        color      : #3989c6;
        font-size  : 1.2em
    }

    .invoice table .qty,
    .invoice table .total,
    .invoice table .unit {
        text-align: right;
        font-size : 1.2em
    }

    .invoice table .no {
        color     : #fff;
        font-size : 1.6em;
        background: #3989c6
    }

    .invoice table .unit {
        background: #ddd
    }

    .invoice table .total {
        background: #3989c6;
        color     : #fff
    }

    .invoice table tbody tr:last-child td {
        border: none
    }

    .invoice table tfoot td {
        background   : 0 0;
        border-bottom: none;
        white-space  : nowrap;
        text-align   : right;
        padding      : 10px 20px;
        font-size    : 1.2em;
        border-top   : 1px solid #aaa
    }

    .invoice table tfoot tr:first-child td {
        border-top: none
    }

    .invoice table tfoot tr:last-child td {
        color     : #3989c6;
        font-size : 1.4em;
        border-top: 1px solid #3989c6
    }

    .invoice table tfoot tr td:first-child {
        border: none
    }

    .invoice footer {
        width     : 100%;
        text-align: center;
        color     : #777;
        border-top: 1px solid #aaa;
        padding   : 8px 0
    }

    @media print {
        .invoice {
            font-size: 11px !important;
            overflow : hidden !important
        }

        .invoice footer {
            position        : absolute;
            bottom          : 10px;
            page-break-after: always
        }

        .invoice>div:last-child {
            page-break-before: always
        }
    }
</style>
<script>
    const { jsPDF } = window.jspdf;

$(document).on('click', '#printInvoice', function () {
    $('.hidden-print').hide();
    setTimeout(() => {
        $('.hidden-print').show();
    }, 3000);
    window.print();
});

$(document).on('click', '#export_to_pdf', function () {
    $('.hidden-print').hide();
    setTimeout(() => {
        $('.hidden-print').show();
    }, 3000);
    var HTML_Width = $("#invoice").width();
    var HTML_Height = $("#invoice").height();
    var top_left_margin = 15;
    var PDF_Width = HTML_Width + (top_left_margin * 2);
    var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
    var canvas_image_width = HTML_Width;
    var canvas_image_height = HTML_Height;

    var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;


    html2canvas($("#invoice")[0], { allowTaint: false }).then(function (canvas) {
        canvas.getContext('2d');

        console.log(canvas.height + "  " + canvas.width);


        var imgData = canvas.toDataURL("image/png", 1.0);
        var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
        pdf.addImage(imgData, 'PNG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);


        for (var i = 1; i <= totalPDFPages; i++) {
            pdf.addPage(PDF_Width, PDF_Height);
            pdf.addImage(imgData, 'PNG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
        }

        pdf.save("sigma-invoice.pdf");
    });
});
</script>
@include('pages.admin.invoices.form.styles')
@include('pages.admin.invoices.form.scripts')
