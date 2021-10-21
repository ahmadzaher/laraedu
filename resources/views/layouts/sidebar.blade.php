<nav id="sidebarMenu" class="text-white bg-dark">
    <div class="d-flex flex-column p-3 text-white bg-dark">
        <div class="mb-5">
        <a href="javascript:void(0)" id="hide_nav" class="closebtn nav-link link-dark mb-5">×</a>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">

            <li class="sidebarCollapse mb-2">
                <a href="#user" data-toggle="collapse" aria-expanded="false" class="arrow-toggle {{ Request::is('profile') ? 'active' : '' }} nav-link link-dark">
                    @auth
                        <div class="form-group d-flex justify-content-around align-items-center">
                            <img src="{{ Auth::user()->getFirstMediaUrl('avatars', 'thumb') ? url(Auth::user()->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg')  }}" alt="avatar" class="avatar rounded img-responsive mr-1">
                            {{ Auth::user()->name }}
                        </div>
                    @endauth
                </a>
                <ul class="collapse lisst-unstyled {{ Request::is('profile') ? 'show' : '' }} p-0 m-1" id="user">
                    <li class="">

                        <a class="nav-link {{ Request::is('profile') ? 'active' : '' }} link-dark ml-2" href="{{ route('profile.edit') }}" >
                            {{ __('Profile') }}
                        </a>
                    </li>
                    <li>

                        <a class="nav-link link-dark ml-2" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
{{--                <a href="{{ route('home') }}" class="nav-link link-dark {{ Request::is('/') ? 'active' : '' }}">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">--}}
{{--                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"></path>--}}
{{--                    </svg>--}}
{{--                    Home--}}
{{--                </a>--}}
{{--            </li>--}}
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link link-dark {{ Request::is('/dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4zM3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z"/>
                        <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A7.988 7.988 0 0 1 0 10zm8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            @if(Auth::user()->can('view-user') || Auth::user()->can('view-role'))
            <?php
                $active = \Request::route()->getName() == 'users' ||
                \Request::route()->getName() == 'roles';
            ?>
            <li class="sidebarCollapse">
                <a href="#users" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                    </svg>
                    Staffs
                </a>
                <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="users">
                    @can('view-user')
                        <li>
                            <a href="{{ route('users') }}" class="nav-link link-dark ml-2 {{ Request::is('user') ? 'active' : '' }}">
{{--                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">--}}
{{--                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>--}}
{{--                                </svg>--}}
                                Staffs List
                            </a>
                        </li>
                    @endcan

                    @can('view-role')
                        <li>
                            <a href="{{ route('roles') }}" class="nav-link link-dark ml-2 {{ Request::is('role') ? 'active' : '' }}">
                                {{--                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">--}}
                                {{--                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>--}}
                                {{--                                </svg>--}}
                                Roles
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
            @endif
            <?php
            $active =
                \Request::route()->getName() == 'teachers' ||
                \Request::route()->getName() == 'departments';
            ?>
            @if(Auth::user()->can('view-teacher') || Auth::user()->can('view-department'))
            <li class="sidebarCollapse">
                <a href="#teachers" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                    </svg>
                    Teachers
                </a>
                <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="teachers">


                    @can('view-teacher')
                        <li>
                            <a href="{{ route('teachers') }}" class="nav-link link-dark ml-2 {{ Request::is('teacher') ? 'active' : '' }}">

                                Teachers List
                            </a>
                        </li>
                    @endcan

                    @can('view-department')
                        <li>
                            <a href="{{ route('departments') }}" class="nav-link link-dark ml-2 {{ Request::is('department') ? 'active' : '' }}">

                                Departments
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
            @endif
            <?php
            $active =
                \Request::route()->getName() == 'students' ||
                \Request::route()->getName() == 'student.add';
            ?>
            @if(Auth::user()->can('view-student') || Auth::user()->can('view-department'))
                <li class="sidebarCollapse">
                    <a href="#student" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                        </svg>
                        Students
                    </a>
                    <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="student">

                        @can('view-student')
                            <li>
                                <a  href="{{ route('students') }}" class="nav-link link-dark ml-2 {{ Request::is('student') ? 'active' : '' }}">

                                    Students List
                                </a>
                            </li>
                        @endcan
                        @can('create-student')
                            <li>
                                <a href="{{ route('student.add') }}" class="nav-link link-dark ml-2 {{ Request::is('student/add') ? 'active' : '' }}">

                                    Add New Student
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            <?php
            $active =
                \Request::route()->getName() == 'classes' ||
                \Request::route()->getName() == 'sections' ||
                \Request::route()->getName() == 'teacher_allocation' ||
                \Request::route()->getName() == 'subjects';
            ?>
            @if(Auth::user()->can('view-class') || Auth::user()->can('view-section') || Auth::user()->can('view-subject'))
                <li class="sidebarCollapse">
                    <a href="#academic" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                            <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/>
                        </svg>
                        Academics
                    </a>
                    <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="academic">

                        @can('view-class')
                            <li>
                                <a href="{{ route('classes') }}" class="nav-link link-dark {{ Request::is('class') ? 'active' : '' }}">

                                    Classes
                                </a>
                            </li>
                        @endcan
                        @can('view-section')
                            <li>
                                <a href="{{ route('sections') }}" class="nav-link link-dark {{ Request::is('section') ? 'active' : '' }}">

                                    Sections
                                </a>
                            </li>
                        @endcan
                        @can('view-teacher')
                            <li>
                                <a href="{{ route('teacher_allocation') }}" class="nav-link link-dark {{ Request::is('teacher_allocation') ? 'active' : '' }}">

                                    Assign Class Teacher
                                </a>
                            </li>
                        @endcan
                        @can('view-subject')
                            <li>
                                <a href="{{ route('subjects') }}" class="nav-link link-dark {{ Request::is('subject') ? 'active' : '' }}">

                                    Subjects
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            <?php
            $active =
                \Request::route()->getName() == 'exams' ||
                \Request::route()->getName() == 'exam_grades';
            ?>
            @if(Auth::user()->can('view-exam') || Auth::user()->can('view-exam-grade'))
                <li class="sidebarCollapse">
                    <a href="#exam" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                            <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/>
                        </svg>
                        Exam
                    </a>
                    <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="exam">

                        @can('view-class')
                            <li>
                                <a href="{{ route('exams') }}" class="nav-link link-dark {{ Request::is('exam') ? 'active' : '' }}">

                                    Exams
                                </a>
                            </li>
                        @endcan
                        @can('view-exam-grade')
                            <li>
                                <a href="{{ route('exam_grades') }}" class="nav-link link-dark {{ Request::is('exam_grade') ? 'active' : '' }}">

                                    Exam Grades
                                </a>
                            </li>
                        @endcan
{{--                        @can('view-teacher')--}}
{{--                            <li>--}}
{{--                                <a href="{{ route('teacher_allocation') }}" class="nav-link link-dark {{ Request::is('teacher_allocation') ? 'active' : '' }}">--}}

{{--                                    Assign Class Teacher--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        @endcan--}}
{{--                        @can('view-subject')--}}
{{--                            <li>--}}
{{--                                <a href="{{ route('subjects') }}" class="nav-link link-dark {{ Request::is('subject') ? 'active' : '' }}">--}}

{{--                                    Subjects--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        @endcan--}}
                    </ul>
                </li>
            @endif
            <?php
            $active =
            \Request::route()->getName() == 'frontend_settings' ||
            \Request::route()->getName() == 'frontend_menu';
            ?>
            @if(Auth::user()->can('view-frontend-settings') || Auth::user()->can('view-frontend-menu'))
                <li class="sidebarCollapse">
                    <a href="#frontend" data-toggle="collapse" aria-expanded="false" class="arrow-toggle @if($active) active @endif nav-link link-dark">
                        <i class="fa fa-globe bi" size="33px"></i>
                        Frontend
                    </a>
                    <ul class="collapse lisst-unstyled @if($active) show @endif p-0 m-1" id="frontend">


                        @can('view-frontend-settings')
                            <li>
                                <a href="{{ route('frontend_settings') }}" class="nav-link link-dark ml-2 {{ Request::is('frontend/settings') ? 'active' : '' }}">

                                    Settings
                                </a>
                            </li>
                        @endcan


                        @can('view-frontend-settings')
                            <li>
                                <a href="{{ route('hero_area') }}" class="nav-link link-dark ml-2 {{ Request::is('frontend/hero_area') ? 'active' : '' }}">

                                    Hero Area
                                </a>
                            </li>
                        @endcan


                        @can('view-frontend-menu')
                            <li>
                                <a href="{{ route('frontend_menu') }}" class="nav-link link-dark ml-2 {{ Request::is('frontend/menu') ? 'active' : '' }}">

                                    Menu
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        </ul>
    </div>

</nav>
