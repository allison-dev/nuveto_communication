<div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        @foreach ($medias as $key => $media)
            @if (isset($media->image))
                @if ($key == 0)
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="{{ $key }}"
                        class="active" aria-current="true" aria-label="Slide {{ $key }}"></button>
                @else
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="{{ $key }}"
                        aria-label="Slide {{ $key }}"></button>
                @endif
            @elseif (isset($media->audio))
                @if ($key == 0)
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="{{ $key }}"
                        class="active" aria-current="true" aria-label="Slide {{ $key }}"></button>
                @else
                    <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="{{ $key }}"
                        aria-label="Slide {{ $key }}"></button>
                @endif
            @endif
        @endforeach
    </div>
    <div class="carousel-inner">
        @foreach ($medias as $key => $media)
            @if (isset($media->image))
                @if ($key == 0)
                    <div class="carousel-item active">
                        <img src="{{ $media->image }}" alt="Sigma-Image {{ $key }}" />
                    </div>
                @else
                    <div class="carousel-item">
                        <img src="{{ $media->image }}" alt="Sigma-Image {{ $key }}" />
                    </div>
                @endif
            @elseif (isset($media->audio))
                @if ($key == 0)
                    <div class="carousel-item active">
                        <audio class="w-50 audio-control" controls>
                            <source src="{{$media->audio}}" type="audio/ogg" />
                        </audio>
                    </div>
                @else
                    <div class="carousel-item">
                        <audio class="w-50 audio-control" controls>
                            <source src="{{$media->audio}}" type="audio/ogg" />
                        </audio>
                    </div>
                @endif
            @endif
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
