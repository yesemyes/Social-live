@extends('layouts.master')
@section('title')
    Reset Password
@endsection

@section('page-content')
    <p class="border_bottom">Reset Password</p>
    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
        {{ csrf_field() }}

        <div class="flex-container mt20 pl20">
            <div class="flex-grow-2">
                <label for="email" class="col-md-4 postTitle">E-Mail Address</label>
                <p class="postTitleWrapper">
                    <input id="email" type="email" class="postTitle" name="email" value="{{ old('email') }}" required>
                </p>
                <button type="submit" class="publish_button">Send Password Reset Link</button>
            </div>
        </div>
    </form>
@endsection