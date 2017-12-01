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
                        <a href="{{URL::to('/')}}"><img src="{{URL::to('img/logo.png')}}" alt="logo" class="w100"></a>
                    </div>
                    <div class="header_center">
                        <span class="megaphone"></span>
                        <div class="dIBlock">
                            <p class="f20">content</p>
                            <p class="f16">promotion</p>
                        </div>
                    </div>
                    <div class="header_right">
                        {{--<a href="api/docs" class="documentation"></a>--}}
                        <a href="{{URL::to('/')}}" class="home"></a>
                    </div>
                    {{--<a href="api/docs" class="docsBtn">Documentation</a>--}}
                </div>
            </div>
        </header>
        <main>
            <div class="container-fluid">
                <div class="flex-container">
                    @include('partials.sidebar')
                    @include('partials.content')
                </div>
            </div>
        </main>
        <footer>
            @include('partials.footer')
        </footer>
    </div>
</body>
</html>
