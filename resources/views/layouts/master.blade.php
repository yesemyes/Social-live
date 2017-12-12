<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.meta')
</head>
<body>
    <div id="wrapper">
        <header>
            <div class="container-fluid">
                <div class="header-container">
                    <div class="logo">
                        <a href="{{URL::to('/')}}">
                            <picture>
                                <source srcset="{{URL::to('img/logo-mobile.png')}}" media="(max-width: 768px)">
                                <source srcset="{{URL::to('img/logo.png')}}">
                                <img src="{{URL::to('img/logo.png')}}" alt="logo">
                            </picture>
                        </a>
                    </div>
                    <div class="header_center">
                        <span class="megaphone"></span>
                        <div class="dIBlock megaphone-text">
                            <p class="f20">content</p>
                            <p class="f16">promotion</p>
                        </div>
                    </div>
                    <div class="header_right">
                        <a href="{{URL::to('/')}}" class="home"></a>
                        @if(Auth::check())
                            <a href="{{url('/logout')}}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();" class="logout"><i class="fa fa-sign-out fa-3" aria-hidden="true"></i></a>
                            <form id="logout-form" action="{{url('/logout')}}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <main>
            <div class="container-fluid">
                <div class="flex-container" id="content-flex">
                    @if(Auth::check())
                    @include('partials.sidebar')
                    @include('partials.content')
                    @else
                        @include('partials.content-login')
                    @endif
                </div>
            </div>
        </main>
        <footer>
            @include('partials.footer')
        </footer>
    </div>
</body>
</html>
