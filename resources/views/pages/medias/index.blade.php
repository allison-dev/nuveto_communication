<!DOCTYPE html>
<html>

<head>
    {{-- <meta http-equiv="refresh" content="60" /> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigma Anexos</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
        media="screen">
    <script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
</head>

<style>
    body {
        background-color: #1d1d1d !important;
        font-family: "Asap", sans-serif;
        color: #989898;
        margin: 10px;
        font-size: 16px;
        overflow-x: hidden;
    }

    #demo {
        height: 100%;
        position: relative;
        overflow: hidden;
    }


    .green {
        background-color: #6fb936;
    }

    .thumb {
        margin-bottom: 30px;
    }

    .page-top {
        margin-top: 85px;
    }


    img.zoom {
        width: 100%;
        height: 200px;
        border-radius: 5px;
        -webkit-transition: all .3s ease-in-out;
        -moz-transition: all .3s ease-in-out;
        -o-transition: all .3s ease-in-out;
        -ms-transition: all .3s ease-in-out;
    }


    .transition {
        -webkit-transform: scale(1.2);
        -moz-transform: scale(1.2);
        -o-transform: scale(1.2);
        transform: scale(1.2);
    }

    .modal-header {

        border-bottom: none;
    }

    .modal-title {
        color: #000;
    }

    .modal-footer {
        display: none;
    }

</style>

<!-- Page Content -->

<body>
    <header>
        <div class="row">
            <div class="col" style="text-align: center; translate3d(0px, -28px, 0px);">
                <a target="_blank" href="https://nuveto.com.br">
                    <img src="{{ asset(config('adminlte.logo_img_invoice')) }}">
                </a>
            </div>
        </div>
    </header>
    <div class="container page-top">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3 thumb">
                <form id="form" action="{{ route('anexos.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="file" type="file" id="file" />
                    <input type="hidden" name="conversationId" value="{{ $conversationId }}">
                    <button style="margin-top: 10px;" class="btn btn-success text-white" type="submit">Upload</button>
                </form>
            </div>
            @foreach ($medias as $key => $media)
                @if (isset($media->image))
                    @if (isset($media->type) && $media->type == 's3')
                        {{-- <img style="width: 500px;" src="/anexos/images/{{ $media->image }}" alt="Image-{{ $key }}"
                    class="img-fluid tm-img"> --}}
                        {{-- <div class="col-sm-6 col-md-4 col-lg-3 item">
                    <a href="/anexos/images/{{ $media->image }}" data-lightbox="photos">
                        <img class="img-fluid" src="/anexos/images/{{ $media->image }}">
                    </a>
                </div> --}}

                        <div class="col-sm-6 col-md-4 col-lg-3 thumb">
                            <a href="/anexos/images/{{ $media->image }}" class="fancybox" rel="ligthbox">
                                <img src="/anexos/images/{{ $media->image }}" class="zoom img-fluid " alt="">
                            </a>
                        </div>
                    @else
                        {{-- <img style="width: 500px;" src="{{ $media->image }}" alt="Image-{{ $key }}"
                    class="img-fluid tm-img"> --}}
                        {{-- <div class="col-sm-6 col-md-4 col-lg-3 item">
                    <a href="{{ $media->image }}" data-lightbox="photos">
                        <img class="img-fluid" src="{{ $media->image }}">
                    </a>
                </div> --}}

                        <div class="col-sm-6 col-md-4 col-lg-3 thumb">
                            <a href="{{ $media->image }}" class="fancybox" rel="ligthbox">
                                <img src="{{ $media->image }}" class="zoom img-fluid " alt="">
                            </a>
                        </div>
                    @endif
                @elseif (isset($media->audio))
                    <div class="col-sm-6 col-md-4 col-lg-3 item">
                        <audio class="w-50 audio-control" controls>
                            <source src="{{ $media->audio }}" type="audio/ogg" />
                        </audio>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</body>

<script>
    $(document).ready(function() {
        $(".fancybox").fancybox({
            openEffect: "none",
            closeEffect: "none"
        });

        $(".zoom").hover(function() {

            $(this).addClass('transition');
        }, function() {

            $(this).removeClass('transition');
        });
    });
</script>
