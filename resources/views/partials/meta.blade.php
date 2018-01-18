<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="iimagine Social Network">
<meta name="author" content="iimagine Social Network">
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

<!-- MetisMenu CSS -->
{{--<link href="{{ url('bower_components/metisMenu/dist/metisMenu.min.css') }}" rel="stylesheet">--}}
<!-- jQuery -->
<script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ url('dist/js/moment.min.js') }}"></script>
<script src="{{ url('dist/js/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Custom CSS -->
{{--<link href="{{ url('dist/css/sb-admin-2.css') }}" rel="stylesheet">--}}
<link href="{{ url('dist/css/custom.css') }}" rel="stylesheet">
<link href="{{ url('dist/css/datetimepicker.css') }}" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
<!-- Custom Fonts -->
<link href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
