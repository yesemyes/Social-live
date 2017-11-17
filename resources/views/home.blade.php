@extends('layouts.admin')

@section('title')
    Dashboard
@endsection

@section('page-content')

    @if( Session::has('message') )
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>{{ Session::get('message') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <h3>Welcome {{ Auth::user()->name }}</h3>
        </div>
    </div>
@endsection

