<div class="sidebar-wrapper">
    <div class="sidebar">
        <ul>
            <li><a href="{{URL::to('/')}}"><i class="dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::to('/create-post')}}"><i class="create"></i> Create Post</a></li>
            <li><a href="{{URL::to('/posts')}}"><i class="posts"></i> Posts</a></li>
            <li><a href="{{URL::to('/networks')}}"><i class="accounts"></i> Accounts</a></li>
            <li><a href=""><i class="settings"></i> Settings</a></li>
        </ul>
    </div>
</div>

{{--
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
                        <li>
                            <a href="{{url('/dashboard')}}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            <a href="{{url('/networks')}}"><i class="fa fa-dashboard fa-fw"></i> Manage accounts</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        --}}