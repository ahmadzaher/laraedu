@if (Auth::check())
    <li><a href="{{ route('dashboard') }}">Home</a></li>
@else
    <li><a href="{{ route('login') }}">Login</a></li>
@endif
<li><a href="#">About</a></li>
<li><a href="#">Courses</a></li>
<li><a href="blog.html">Blog</a></li>
<li><a href="contact.html">Contact</a></li>
@if (Auth::check())
    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
            {{ __('Logout') }}</a></li>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endif
