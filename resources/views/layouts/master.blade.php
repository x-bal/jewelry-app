<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }} | {{ $title }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- ================== BEGIN core-css ================== -->
    <link href="{{ asset('/') }}css/vendor.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}css/apple/app.min.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/ionicons/css/ionicons.min.css" rel="stylesheet" />
    <!-- ================== END core-css ================== -->

    <!-- ================== BEGIN page-css ================== -->
    <link href="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/bootstrap-calendar/css/bootstrap_calendar.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="{{ asset('/') }}plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
    <!-- ================== END page-css ================== -->

    @stack('style')
</head>

<body>
    <div id="app" class="app app-header-fixed app-sidebar-fixed">
        <div id="header" class="app-header">
            <div class="navbar-header">
                <a href="/dashboard" class="navbar-brand"><span class="navbar-logo"><i class="ion-ios-browsers"></i></span> <b class="me-1">Jewelry</b> App</a>
                <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <div class="navbar-nav">
                <div class="navbar-item navbar-user dropdown">
                    <a href="#" class="navbar-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ asset('/') }}img/user/user-13.jpg" alt="" />
                        <span>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <b class="caret"></b>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end me-1">
                        <a href="javascript:;" class="dropdown-item">Edit Profile</a>
                        <a href="javascript:;" class="dropdown-item"><span class="badge bg-danger float-end rounded-pill">2</span> Inbox</a>
                        <a href="javascript:;" class="dropdown-item">Calendar</a>
                        <a href="javascript:;" class="dropdown-item">Setting</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">Log Out</a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div id="sidebar" class="app-sidebar">
            <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
                <div class="menu">
                    <div class="menu-profile">
                        <a href="javascript:;" class="menu-profile-link" data-toggle="app-sidebar-profile" data-target="#appSidebarProfileMenu">
                            <div class="menu-profile-cover with-shadow"></div>
                            <div class="menu-profile-image">
                                <img src="{{ asset('/') }}img/user/user-13.jpg" alt="" />
                            </div>
                            <div class="menu-profile-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="menu-caret ms-auto"></div>
                                </div>
                                <small>Front end developer</small>
                            </div>
                        </a>
                    </div>
                    <div id="appSidebarProfileMenu" class="collapse">
                        <div class="menu-item pt-5px">
                            <a href="javascript:;" class="menu-link">
                                <div class="menu-icon"><i class="fa fa-cog"></i></div>
                                <div class="menu-text">Settings</div>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="javascript:;" class="menu-link">
                                <div class="menu-icon"><i class="fa fa-pencil-alt"></i></div>
                                <div class="menu-text"> Send Feedback</div>
                            </a>
                        </div>
                        <div class="menu-item pb-5px">
                            <a href="javascript:;" class="menu-link">
                                <div class="menu-icon"><i class="fa fa-question-circle"></i></div>
                                <div class="menu-text"> Helps</div>
                            </a>
                        </div>
                        <div class="menu-divider m-0"></div>
                    </div>

                    <div class="menu-header">Navigation</div>
                    <div class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <a href="/dashboard" class="menu-link">
                            <div class="menu-icon">
                                <i class="ion-ios-home bg-blue"></i>
                            </div>
                            <div class="menu-text">Dashboard</div>
                        </a>
                    </div>

                    <div class="menu-item has-sub">
                        <a href="javascript:;" class="menu-link">
                            <div class="menu-icon">
                                <i class="ion-ios-apps bg-indigo"></i>
                            </div>
                            <div class="menu-text">Data Master</div>
                            <div class="menu-caret"></div>
                        </a>
                        <div class="menu-submenu">
                            <div class="menu-item">
                                <a href="{{ route('users.index') }}" class="menu-link">
                                    <div class="menu-text">Data User</div>
                                </a>
                                <a href="{{ route('locators.index') }}" class="menu-link">
                                    <div class="menu-text">Data Locator</div>
                                </a>
                                <a href="{{ route('satuan.index') }}" class="menu-link">
                                    <div class="menu-text">Data Satuan</div>
                                </a>
                                <a href="{{ route('tipe-barang.index') }}" class="menu-link">
                                    <div class="menu-text">Data Tipe Barang</div>
                                </a>
                                <a href="{{ route('barang.index') }}" class="menu-link">
                                    <div class="menu-text">Data Barang</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="menu-item has-sub">
                        <a href="javascript:;" class="menu-link">
                            <div class="menu-icon">
                                <i class="ion-ios-cube bg-green"></i>
                            </div>
                            <div class="menu-text">Inventory</div>
                            <div class="menu-caret"></div>
                        </a>
                        <div class="menu-submenu">
                            <div class="menu-item">
                                <a href="{{ route('stok-opname.index') }}" class="menu-link">
                                    <div class="menu-text">Stok Opname</div>
                                </a>
                                <a href="{{ route('lost-stok.index') }}" class="menu-link">
                                    <div class="menu-text">Lost Stok</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <a href="/dashboard" class="menu-link">
                            <div class="menu-icon">
                                <i class="ion-ios-home bg-blue"></i>
                            </div>
                            <div class="menu-text">Penjualan</div>
                        </a>
                    </div>

                    <div class="menu-item d-flex">
                        <a href="javascript:;" class="app-sidebar-minify-btn ms-auto" data-toggle="app-sidebar-minify"><i class="ion-ios-arrow-back"></i>
                            <div class="menu-text">Collapse</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-sidebar-bg"></div>
        <div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link"></a></div>

        <div id="content" class="app-content">

            <ol class="breadcrumb float-xl-end">
                @foreach($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item"><a href="javascript:;">{{ $breadcrumb }}</a></li>
                @endforeach
            </ol>

            <h1 class="page-header">{{ $title }}</h1>

            @yield('content')

        </div>

        <a href="javascript:;" class="btn btn-icon btn-circle btn-primary btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
    </div>

    <!-- ================== BEGIN core-js ================== -->
    <script src="{{ asset('/') }}js/vendor.min.js"></script>
    <script src="{{ asset('/') }}js/app.min.js"></script>
    <script src="{{ asset('/') }}js/theme/apple.min.js"></script>
    <!-- ================== END core-js ================== -->

    <!-- ================== BEGIN page-js ================== -->
    <script src="{{ asset('/') }}plugins/d3/d3.min.js"></script>
    <script src="{{ asset('/') }}plugins/nvd3/build/nv.d3.min.js"></script>
    <script src="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
    <script src="{{ asset('/') }}plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
    <script src="{{ asset('/') }}plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
    <script src="{{ asset('/') }}js/demo/dashboard-v2.js"></script>
    <!-- ================== END page-js ================== -->

    @stack('script')
</body>

</html>