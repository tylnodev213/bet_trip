<nav class="navbar top-navbar navbar-expand-md navbar-dark">
    <div class="navbar-header">
        <!-- This is for the sidebar toggle which is visible on mobile only -->
        <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                class="ti-menu ti-close"></i></a>
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <!-- Logo icon -->
            <b class="logo-icon">
                <!-- Light Logo icon -->
                <img src="{{ asset('admins/assets/images/logo.png') }}" alt="homepage"
                     class="light-logo"/>
            </b>
            <!--End Logo icon -->
            <!-- Logo text -->
            <span class="logo-text" style="margin-left: 20px;">
                 <!-- dark Logo text -->
                 <img height="40" src="{{ asset('images/logo.png') }}" alt="homepage" class="dark-logo"/>
            </span>
        </a>
        <hr>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Toggle which is visible on mobile only -->
        <!-- ============================================================== -->
        <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
           data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
           aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
    </div>
    <!-- ============================================================== -->
    <!-- End Logo -->
    <!-- ============================================================== -->
    <div class="navbar-collapse collapse" id="navbarSupportedContent">
        <!-- ============================================================== -->
        <!-- toggle and nav items -->
        <!-- ============================================================== -->
        <ul class="navbar-nav float-left mr-auto">
            <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light"
                                                      href="javascript:void(0)" data-sidebartype="mini-sidebar"><i
                        class="mdi mdi-menu font-24"></i></a></li>

            <!-- ============================================================== -->
            <!-- Search -->
            <!-- ============================================================== -->
            {{--            <li class="nav-item search-box"><a class="nav-link waves-effect waves-dark"--}}
            {{--                                               href="javascript:void(0)"><i class="ti-search"></i></a>--}}
            {{--                <form class="app-search position-absolute">--}}
            {{--                    <input type="text" class="form-control" placeholder="Search &amp; enter"> <a--}}
            {{--                        class="srh-btn"><i class="ti-close"></i></a>--}}
            {{--                </form>--}}
            {{--            </li>--}}
        </ul>
        <!-- ============================================================== -->
        <!-- Right side toggle and nav items -->
        <!-- ============================================================== -->
        <ul class="navbar-nav float-right">
            <!-- ============================================================== -->
            <!-- create new -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- End Messages -->
            <li class="nav-item dropdown notificationNewTour">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href=""
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                        src="{{ asset('admins/assets/images/notification.png') }}" alt="user" class="rounded-circle"
                        width="31"></a>
                <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY notificationNewTourDropdown">
                    @foreach (Auth::user()->notifications as $notification)
                        <p class="dropdown-item" href="#">
                            <span>Xác nhận booking mới!</span><br>
                            <small>{{ $notification->data['content'] }}</small>
                            <a href="{{ $notification->data['url'] }}">Chi tiết</a>
                        </p>
                    @endforeach
                </div>
            </li>
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href=""
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                        src="{{ asset('admins/assets/images/users/1.jpg') }}" alt="user" class="rounded-circle"
                        width="31"></a>
                <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                    <span class="with-arrow"><span class="bg-primary"></span></span>
                    <div class="d-flex no-block align-items-center p-15 bg-primary text-white mb-2">
                        <div class="ml-2">
                            <h4 class="mb-0">{{ \Illuminate\Support\Facades\Auth::user()->name }}</h4>
                            <p class=" mb-0">{{ \Illuminate\Support\Facades\Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('admin.password.change') }}">
                        <i class="ti-user mr-1 ml-1"></i> Đổi mật khẩu
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.logout') }}">
                        <i class="fa fa-power-off mr-1 ml-1"></i> Đăng xuất
                    </a>
                </div>
            </li>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
        </ul>
    </div>
</nav>
