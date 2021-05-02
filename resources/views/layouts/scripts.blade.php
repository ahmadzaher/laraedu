<script src="{{ asset('plugins/jquery/jquery.js') }}"></script>
<script src="{{ asset('plugins/jquery/jquery.validate.js') }}"></script>
<script src = "{{ asset('plugins/jquery/jquery.dataTables.min.js') }}" defer ></script>
<script src = "{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}" defer ></script>
<script src = "{{ asset('plugins/bootstrap/js/dataTables.bootstrap4.min.js') }}" defer ></script>
<script>

    $(function () {
        $('#show_nav').on('click', function (e){
            var screenWidth = $( document ).width();
            $('#sidebarMenu').css('width', '250px');
            if(screenWidth > 600)
                $('#main-content').css('margin-left', '250px');
            $('.sidebar-toggle').css('display', 'none');
        });
        $('#hide_nav').on('click', function (e){
            $('#sidebarMenu').css('width', 0);
            $('#main-content').css('margin-left', 0);
            $('.sidebar-toggle').css('display', 'block');
        });

    })
</script>
