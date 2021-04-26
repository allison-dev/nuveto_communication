@component('mail::layout')
    {{-- Header --}}

    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
        <img border="0" hspace="0" align="center" vspace="0" width="570" class="mobile-full"
        style="border: 0px; display: block; vertical-align: top; height: auto; margin: 0 auto; color:
        #3f3f3f; font-size: 13px; font-family: Arial, Helvetica, sans-serif; width: 100%; max-width:
        570px; height: auto;" src="https://mosaico.io/srv/f-yis60u7/img?src=https%3A%2F%2Fmosaico.io%2Ffiles%2Fyis60u7%2FHeader-email_Nuveto-sigma%2520%25281%2529.png&amp;method=resize&amp;params=570%2Cnull">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <img border="0" hspace="0" align="center" vspace="0" width="570" class="mobile-full" style="border: 0px; display: block; vertical-align: top; height: auto; margin: 0 auto; color: #3f3f3f; font-size: 13px; font-family: Arial, Helvetica, sans-serif; width: 100%; max-width: 570px; height: auto;" src="https://mosaico.io/srv/f-yis60u7/img?src=https%3A%2F%2Fmosaico.io%2Ffiles%2Fyis60u7%2FFooter-email_sigma-2.png&amp;method=resize&amp;params=570%2Cnull">
        @endcomponent
    @endslot
@endcomponent
