<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CRUD Laravel">
    <meta name="author" content="Hector Dolo">
    <meta name="google-signin-client_id" content="149135955700-mptte3s01jl1cloulu7hceequp1f9jq1.apps.googleusercontent.com">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <!-- Bootstrap Core CSS -->
    <link href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="{{ url('bower_components/metisMenu/dist/metisMenu.min.css') }}" rel="stylesheet">

    <!-- jQuery -->
    <script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script>

    <!-- Custom CSS -->
    <link href="{{ url('dist/css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{ url('dist/css/custom.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    @yield('header-scripts')

</head>

<body id="wrapper">

<nav class="navbar navbar-default navbar-static-top navbar-blue" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <!-- Branding Image -->
        <a class="navbar-brand mainLogo" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>
    {{--<div class="mainNav">
        <ul>
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i>Dashboard</a></li>
            <li><a href="#"><i class="fa fa-dashboard"></i>Create post</a></li>
            <li><a href="#"><i class="fa fa-dashboard"></i>Manage posts</a></li>
            <li><a href="{{ url('/networks') }}"><i class="fa fa-dashboard"></i>Manage accounts</a></li>
        </ul>
    </div>--}}
    <ul class="nav navbar-top-links navbar-right">
        <li><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        <li><a href="{{ url('/create-post') }}"><i class="fa fa-plus"></i>Create post</a></li>
        <li><a href="{{ url('/manage-posts') }}"><i class="fa fa-bars"></i>Manage posts</a></li>
        <li><a href="{{ url('/networks') }}"><i class="fa fa-users"></i>Manage accounts</a></li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-cog"></i> Settings <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li>
                    <a href="{{ url('/logout') }}"
                       onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out fa-fw"></i> Logout
                    </a>

                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
                <li>
                    <a href="{{ url('/account') }}">Account</a>
                </li>
            </ul>
        </li>
    </ul>

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                {{--<li class="sidebar-search">
                    <div class="input-group custom-search-form">

                        <div class="input-group">
                            {!! Form::text('search', null, ['class' => 'form-control', 'placeholder' => 'Search...']) !!}
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>

                    </div>
                </li>--}}
                <li>
                    <a href="{{url('/dashboard')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    <a href="{{url('/networks')}}"><i class="fa fa-dashboard fa-fw"></i> Manage accounts</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="page-wrapper">
    <div class=""container>
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">@yield('page-header')</h3>
            </div>
        </div>
    </div>

    @yield('page-content')

</div>
<!-- /#page-wrapper -->


<!-- Bootstrap Core JavaScript -->
<script src="{{ url('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="{{ url('bower_components/metisMenu/dist/metisMenu.min.js') }}"></script>

<!-- Custom Theme JavaScript -->
<script src="{{ url('dist/js/sb-admin-2.js') }}"></script>
<script src="{{ url('dist/js/custom.js') }}"></script>

<script src="/js/index.js"></script>

@yield('footer-scripts')

</body>
</html>
