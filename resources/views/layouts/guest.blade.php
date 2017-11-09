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
    <script src="{{ url('bower_components/jquery/dist/jquery.js') }}"></script>

    <!-- Custom CSS -->
    <link href="{{ url('dist/css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{ url('css/guest.css') }}" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    {{--<link rel="stylesheet" href="{{ url('bower_components/jquery/src/css/jquery-ui.min.css') }}">--}}

    {{--<script src="{{ url('js/jquery.js') }}"></script>--}}
    {{--<script src="{{ url('bower_components/jquery/src/jquery-ui.min.js') }}"></script>--}}

    @yield('header-scripts')

</head>

<body id="wrapper">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li><a href="{{ url('/login') }}">Login</a></li>
            <li><a href="{{ url('/register') }}">Register</a></li>
        </ul>
    </nav>

    @yield('page-content')

<script src="{{ url('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="{{ url('bower_components/metisMenu/dist/metisMenu.min.js') }}"></script>

<!-- Custom Theme JavaScript -->
<script src="{{ url('dist/js/sb-admin-2.js') }}"></script>

@yield('footer-scripts')

</body>
</html>
