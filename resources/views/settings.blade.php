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
    @if( Session::has('message_success_invite') )
        <p class="msg_success">{{ Session::get('message_success_invite') }}</p>
    @elseif( Session::has('message_error_invite') )
        <p class="msg_error">{{ Session::get('message_error_invite') }}</p>
    @endif
    <p class="border_bottom">Account Settings</p>
    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/account/invite/{{ Auth::user()->id }}" method="post" autocomplete="asdasd">
        {{ csrf_field() }}
        <div class="flex-container-wrap mt20 pl20 account_form">
            <div class="fb50p">
                <label for="invite_subject">Subject</label>
                <p class="postTitleWrapper">
                    <input type="text" id="invite_subject" required="required" value="Invite" name="invite_subject" class="postTitle" placeholder="Subject">
                </p>
                <label for="invite_email">To</label>
                <p class="postTitleWrapper">
                    <input type="email" id="invite_email" required="required" value="" name="invite_email" class="postTitle" placeholder="Friend Email">
                </p>
                <label for="invite_message">Message</label>
                <p class="postTitleWrapper">
                    <textarea name="invite_message" id="invite_message" placeholder="Invite Your Friend" cols="30" rows="10"></textarea>
                </p>
                <button name="invite" value="invite" class="publish_button">Send Email</button>
            </div>
        </div>
    </form>

    <form action="/account/update/{{ Auth::user()->id }}" method="post" autocomplete="asdasd">
        {{ csrf_field() }}
        <div class="flex-container-wrap mt20 pl20 account_form">
            <div class="fb50p">
                <label for="username">User Name</label>
                <p class="postTitleWrapper">
                    <input type="text" id="username" required="required" value="{{ Auth::user()->name }}" name="name" class="postTitle" placeholder="User Name">
                </p>
                <label for="email">Email</label>
                <p class="postTitleWrapper">
                    <input type="email" id="email" required="required" value="{{ Auth::user()->email }}" name="email" class="postTitle" placeholder="Email">
                </p>
            </div>
            <div class="fb50p">
                <label for="old_password">Old Password</label>
                <p class="postTitleWrapper">
                    <input type="password" id="old_password" required="required" name="old_password" class="inputPswdStyle" placeholder="Old Password">
                    <label class="pl10"><input type="checkbox" class="show_pass" onclick="oldPassword()"></label>
                </p>
                <label for="new_password">Password</label>
                <p class="postTitleWrapper">
                    <input type="password" id="new_password" required="required" name="new_password" class="inputPswdStyle" placeholder="New Password">
                    <label class="pl10"><input type="checkbox" class="show_pass" onclick="newPassword()"></label>
                </p>
                <label for="confirm_password">Confirm Password</label>
                <p class="postTitleWrapper">
                    <input type="password" id="confirm_password" required="required" name="confirm_password" class="inputPswdStyle" placeholder="Confirm Password">
                    <label class="pl10"><input type="checkbox" class="show_pass" onclick="confirmPassword()"></label>
                </p>
                <button name="change_account" value="change" class="save_draft">Change</button>
            </div>
        </div>
    </form>

@endsection
