@extends('layouts.master')

@section('page-content')
    <div class="login-card">
        <h1>Log-in</h1><br>
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <div class="col-md-6">
                    <input id="email" type="email" class="form-control" placeholder="E-Mail Address" name="email" value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="col-md-6">
                    <input id="password" type="password" class="form-control" placeholder="Password" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <label for="remember"><input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : ''}}> Remember Me</label>

            <input type="submit" name="login" class="login login-submit" value="login">
        </form>

        <div class="login-help">
            <a href="{{url('/register')}}">Register</a> • <a href="{{ url('/password/reset') }}">Forgot Password</a>
        </div>
    </div>

@endsection
