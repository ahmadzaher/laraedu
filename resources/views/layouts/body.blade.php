
<body style="height: auto">
<div class="container-fluid">
    @auth
        @include('layouts.sidebar')
    @endauth
        <main @auth id="main-content" @endauth class="">
            <a style="width: 30px;" href="#" data-toggle="sidebar" class="sidebar-toggle m-2" id="show_nav">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"  class=" bi-text-right text-black-50" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </a>
            <div class="mt-5 mb-5">
                <div class="container">

                    @include('layouts.alerts')
                    @yield('content')
                </div>
            </div>
        </main>
</div>
</body>
