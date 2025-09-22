<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-12 col-sm-12 text-center">
                <span class="text-muted fs-11">{{ __('Copyright') }} © {{ date("Y") }} <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>. {{ __('All rights reserved') }}</span>
            </div>
            <div class="col-md-12 col-sm-12 text-center">
                <span class="fs-10 font-weight-bold text-info">{{ config('app.version') }}</span>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->

<!-- Back to top -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-double-up"></i></a>

<!-- Jquery -->
<script src="{{URL::asset('plugins/jquery/jquery-3.6.0.min.js')}}"></script>

<!-- Bootstrap 5 -->
<script src="{{URL::asset('plugins/bootstrap-5.0.2/js/bootstrap.bundle.min.js')}}"></script>

<!-- Sidemenu -->
<script src="{{URL::asset('plugins/sidemenu/sidemenu.js')}}"></script>

<!-- P-scroll -->
<script src="{{URL::asset('plugins/p-scrollbar/p-scrollbar.js')}}"></script>
<script src="{{URL::asset('plugins/p-scrollbar/p-scroll.js')}}"></script>

@yield('js')

<!-- Awselect JS -->
<script src="{{URL::asset('plugins/awselect/awselect-custom.js')}}"></script>
<script src="{{theme_url('js/awselect.js')}}"></script>

<!-- Simplebar JS -->
<script src="{{URL::asset('plugins/simplebar/js/simplebar.min.js')}}"></script>

<!-- Tippy JS -->
<script src="{{URL::asset('plugins/tippy/popper.min.js')}}"></script>
<script src="{{URL::asset('plugins/tippy/tippy-bundle.umd.min.js')}}"></script>

<!-- Toastr JS -->
<script src="{{URL::asset('plugins/toastr/toastr.min.js')}}"></script>

<!-- Custom js-->
<script src="{{theme_url('js/custom.js')}}"></script>


<!-- Mark as Read JS-->
<script type="text/javascript">

    function sendMarkRequest(id = null) {
        return $.ajax("{{ route('user.notifications.mark') }}", {
            method: 'POST',
            data: {"_token": "{{ csrf_token() }}", id}
        });
    }


    tippy('[data-tippy-content]', {
        animation: 'scale-extreme',
        theme: 'material',
    });

    const words_info = document.getElementById('nav-info-words');
    const images_info = document.getElementById('nav-info-images');
    const characters_info = document.getElementById('nav-info-characters');
    const minutes_info = document.getElementById('nav-info-minutes');
    
    tippy('[data-tippy-words]', {
        animation: 'scale-extreme',
        theme: 'material',
        content: words_info.innerHTML,
        allowHTML: true,
    });

    tippy('[data-tippy-images]', {
        animation: 'scale-extreme',
        theme: 'material',
        content: images_info.innerHTML,
        allowHTML: true,
    });

    tippy('[data-tippy-characters]', {
        animation: 'scale-extreme',
        theme: 'material',
        content: characters_info.innerHTML,
        allowHTML: true,
    });

    tippy('[data-tippy-minutes]', {
        animation: 'scale-extreme',
        theme: 'material',
        content: minutes_info.innerHTML,
        allowHTML: true,
    });

    toastr.options.showMethod = 'slideDown';
    toastr.options.hideMethod = 'slideUp';
    toastr.options.progressBar = true;


    $(function(){

        var ua =navigator.userAgent;
        if(ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1 || ua.indexOf('iPod')  > -1){
            var start = "touchstart";
            var move  = "touchmove";
            var end   = "touchend";
        } else{
            var start = "mousedown";
            var move  = "mousemove";
            var end   = "mouseup";
        }
        var ink, d, x, y;
        $(".ripple").on(start, function(e){
        
            if($(this).find(".ink").length === 0){
            $(this).prepend("<span class='ink'></span>");
        }
            
        ink = $(this).find(".ink");
        ink.removeClass("animate");
        
        if(!ink.height() && !ink.width()){
            d = Math.max($(this).outerWidth(), $(this).outerHeight());
            ink.css({height: d, width: d});
        }
        
        x = e.originalEvent.pageX - $(this).offset().left - ink.width()/2;
        y = e.originalEvent.pageY - $(this).offset().top - ink.height()/2;
        
        ink.css({top: y+'px', left: x+'px'}).addClass("animate");

        });

    });

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.open-toggle');
        const toggleIcon = document.querySelector('.menu-toggle-icon');
        let isRotated = false;
        
        toggleBtn.addEventListener('click', function(e) {
            isRotated = !isRotated;
            if(isRotated) {
                toggleIcon.style.transform = 'rotate(180deg)';
            } else {
                toggleIcon.style.transform = 'rotate(0deg)';
            }
        });
    });
   
</script>

