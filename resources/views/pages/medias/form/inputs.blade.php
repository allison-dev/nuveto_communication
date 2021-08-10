@foreach ($medias as $key => $media)
    @if (isset($media->image))
        @if (isset($media->type) && $media->type == 's3')
            {{-- <img style="width: 500px;" src="/anexos/images/{{ $media->image }}" alt="Image-{{ $key }}"
                class="img-fluid tm-img"> --}}
           {{--  <div class="col-sm-6 col-md-4 col-lg-3 item">
                <a href="/anexos/images/{{ $media->image }}" data-lightbox="photos">
                    <img class="img-fluid" src="/anexos/images/{{ $media->image }}">
                </a>
            </div> --}}

            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
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

            <div class="col-lg-3 col-md-4 col-xs-6 thumb">
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
