@extends('layouts.master')

@section('title') Dashboard @endsection

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

    <p class="border_bottom">Welcome <b>{{ Auth::user()->name }} !</b></p>

    <h3 class="mt20 f16">All post</h3>

    <div class="flex-container mt20 pl20 border_bottom all_post_title">
        <div class="flex-grow-1">
            <p class="tUppercase">title</p>
        </div>
        <div class="status">
            <p class="tUppercase">status</p>
        </div>
        <div class="action">
            <p class="tUppercase">action</p>
        </div>
    </div>

    @foreach( $posts as $post )
    <div class="flex-container mt20 pl20 border_bottom">
        <div class="flex-grow-1">
            <p class="post_title_detalis">{{$post->title}}</p>
        </div>
        <div class="flex-grow-1">
            <p class="created">Created {{date('M d, Y', strtotime($post->updated_at))}}</p>
        </div>
        <div class="flex-grow-1">
            <p class="time">{{date('H:i a', strtotime($post->updated_at))}}</p>
        </div>
        <div class="flex-grow-1">
            <p class="name">{{ $user->name }}</p>
        </div>
        <div class="success">
            <span class="circle-green"></span>
            <p class="success_text_green">Success</p>
        </div>
        <div class="share">
            <a href="{{ url('/publish-post/'.$post->id) }}" class="share_text">SHARE</a>
        </div>
    </div>
    @endforeach


@endsection

