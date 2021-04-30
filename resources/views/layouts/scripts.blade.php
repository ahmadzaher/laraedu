<script src="{{ asset('plugins/jquery/jquery.js') }}"></script>
<script src="{{ asset('plugins/jquery/jquery.validate.js') }}"></script>
<script src = "{{ asset('plugins/jquery/jquery.dataTables.min.js') }}" defer ></script>
<script src = "{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}" defer ></script>
<script src = "{{ asset('plugins/bootstrap/js/dataTables.bootstrap4.min.js') }}" defer ></script>
<script>

    $(function () {
        $('#navbarDropdown').on('click', function (){
            $('#navbarDropdown').dropdown();
        })
        $('#show_nav').on('click', function (e){
            $('#sidebarMenu').css('width', '250px');
            $('#main-content').css('margin-left', '250px');
            $('.sidebar-toggle').css('display', 'none');
        });
        $('#hide_nav').on('click', function (e){
            $('#sidebarMenu').css('width', 0);
            $('#main-content').css('margin-left', 0);
            $('.sidebar-toggle').css('display', 'block');
        });

        $(document).mouseup(function(e)
        {
            var screenWidth = $( document ).width();
            var container = $('#sidebarMenu');

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0 && screenWidth <= 1200)
            {
                container.css('width', '0');
                $('#main-content').css('margin-left', '0');
            }
        });
    })
</script>
