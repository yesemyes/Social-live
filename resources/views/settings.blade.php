@extends('layouts.master')

@section('title')
    My Account ( {{ Auth::user()->name }} )
@endsection

@section('page-content')

    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @endif
    <p class="border_bottom">Account Settings</p>
    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/settings/update/{{ Auth::user()->id }}" method="post">
        {{ csrf_field() }}
        <div class="flex-container mt20 pl20 create_post">
            <div class="flex-grow-2">
                <label for="postTitle">User Name</label>
                <p class="postTitleWrapper">
                    <input type="text" id="username" required="required" value="{{ Auth::user()->name }}" name="username" class="postTitle" placeholder="User Name">
                </p>
                <label for="postContent">Email</label>
                <p class="postTitleWrapper">
                    <input type="email" id="email" required="required" value="{{ Auth::user()->email }}" name="email" class="postTitle" placeholder="Email">
                </p>
                <label for="postContent">Password</label>
                <p class="postTitleWrapper">
                    <input type="password" id="password" required="required" name="password" class="postTitle" placeholder="Password">
                </p>
                <button name="change" class="save_draft">Change</button>
            </div>
        </div>
    </form>

@endsection
