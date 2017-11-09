@extends('layouts.admin')

@section('title')
    My Account ( {{ Auth::user()->name }} )
@endsection

@section('page-header')
    Account
@endsection

@section('page-content')

    @if( Session::has('msg') )
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    {{ Session::get('msg') }}
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Settings
                </div>
                <div class="panel-body">
                    <form action="/account/update/{{ Auth::user()->id }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}">
                            </div>
                            <div class="col-lg-6">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ Auth::user()->email }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password">
                            </div>
                            <div class="col-lg-6">
                                <input type="submit" name="change_account" value="change">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
