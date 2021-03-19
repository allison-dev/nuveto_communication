{{-- <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#"
        @if(config('adminlte.sidebar_collapse_remember'))
            data-enable-remember="true"
        @endif
        @if(!config('adminlte.sidebar_collapse_remember_no_transition'))
            data-no-transition-after-reload="false"
        @endif
        @if(config('adminlte.sidebar_collapse_auto_size'))
            data-auto-collapse-size="{{ config('adminlte.sidebar_collapse_auto_size') }}"
        @endif>
        <i class="fas fa-bars"></i>
        <span class="sr-only">{{ __('adminlte::adminlte.toggle_navigation') }}</span>
    </a>
</li>
 --}}

 <div class="container-menu ml-auto" data-widget="pushmenu" onclick="changeMenu(this)">
    <div class="bar1"></div>
    <div class="bar2"></div>
    <div class="bar3"></div>
</div>

<style>
    .container-menu {
        display  : inline-block;
        cursor   : pointer;
        transform: translate3d(10px, 20px, 0px);
    }

    .bar1,
    .bar2,
    .bar3 {
        width           : 20px;
        height          : 3px;
        background-color: #f2282b;
        margin          : 2px auto;
    }

    .change .bar1 {
        -webkit-transform: rotateZ(45deg) translateY(11px);
        -moz-transform   : rotateZ(45deg) translateY(11px);
        -ms-transform    : rotateZ(45deg) translateY(11px);
        -o-transform     : rotateZ(45deg) translateY(11px);
        transform        : rotateZ(45deg) translateY(2px);
        width            : 12px;
        transition: all 0.5s ease;
    }

    .change .bar2 {
        width            : 15px;
        -webkit-transform: translateX(-25px);
        -moz-transform   : translateX(-25px);
        -ms-transform    : translateX(-25px);
        -o-transform     : translateX(-25px);
        transform        : translateX(-7px);
        transition: all 0.5s ease;
    }

    .change .bar3 {
        -webkit-transform: rotateZ(-45deg) translateY(-11px);
        -moz-transform   : rotateZ(-45deg) translateY(-11px);
        -ms-transform    : rotateZ(-45deg) translateY(-11px);
        -o-transform     : rotateZ(-45deg) translateY(-11px);
        transform        : rotateZ(-45deg) translateY(-2px);
        width            : 12px;
        transition: all 0.5s ease;
    }
</style>

<script>
    function changeMenu(x) {
        x.classList.toggle("change");
    }
</script>

{{-- <main>
    <div class="col">
      <div class="con">
        <div class="bar arrow-top"></div>
        <div class="bar arrow-middle"></div>
        <div class="bar arrow-bottom"></div>
      </div>
    </div>
  </main>

<style>
     html,
 body {
   height: 100%;
   width: 100%;
   margin: 0;
   background: #0B3142;
 }

 main {
   position: relative;
   top: 50%;
 }

 .con,
 .special-con {
   cursor: pointer;
   display: inline-block;
 }

 .bar {
   display: block;
   height: 5px;
   width: 50px;
   background: #6FFFE9;
   margin: 10px auto;
 }

 .con {
   width: auto;
   margin: 0 auto;
   -webkit-transition: all .7s ease;
   -moz-transition: all .7s ease;
   -ms-transition: all .7s ease;
   -o-transition: all .7s ease;
   transition: all .7s ease;
 }

.con:hover .bar,.special-con:hover .bar {
  background-color: #f92c8c;
}

 .col {
   display: inline-block;
   width: 24%;
   text-align: center;
   height: auto;
   position: relative;
 }

 .middle {
   margin: 0 auto;
 }

 .bar {
   -webkit-transition: all .7s ease;
   -moz-transition: all .7s ease;
   -ms-transition: all .7s ease;
   -o-transition: all .7s ease;
   transition: all .7s ease;
 }

 .con:hover .top {
   -webkit-transform: translateY(15px) rotateZ(45deg);
   -moz-transform: translateY(15px) rotateZ(45deg);
   -ms-transform: translateY(15px) rotateZ(45deg);
   -o-transform: translateY(15px) rotateZ(45deg);
   transform: translateY(15px) rotateZ(45deg);
 }

 .con:hover .bottom {
   -webkit-transform: translateY(-15px) rotateZ(-45deg);
   -moz-transform: translateY(-15px) rotateZ(-45deg);
   -ms-transform: translateY(-15px) rotateZ(-45deg);
   -o-transform: translateY(-15px) rotateZ(-45deg);
   transform: translateY(-15px) rotateZ(-45deg);
 }

 .con:hover .middle {
   width: 0;
 }

 .con:hover .arrow-top {
   -webkit-transform: rotateZ(45deg) translateY(11px);
   -moz-transform: rotateZ(45deg) translateY(11px);
   -ms-transform: rotateZ(45deg) translateY(11px);
   -o-transform: rotateZ(45deg) translateY(11px);
   transform: rotateZ(45deg) translateY(11px);
   width: 25px;
 }

 .con:hover .arrow-middle {
   -webkit-transform: translateX(-25px);
   -moz-transform: translateX(-25px);
   -ms-transform: translateX(-25px);
   -o-transform: translateX(-25px);
   transform: translateX(-25px);
 }
 .con:hover .arrow-bottom {
   -webkit-transform: rotateZ(-45deg) translateY(-11px);
   -moz-transform: rotateZ(-45deg) translateY(-11px);
   -ms-transform: rotateZ(-45deg) translateY(-11px);
   -o-transform: rotateZ(-45deg) translateY(-11px);
   transform: rotateZ(-45deg) translateY(-11px);
   width: 25px;
 }
</style> --}}
