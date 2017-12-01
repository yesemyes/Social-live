@extends('layouts.master')
@section('title')
    All Posts
@endsection
@section('page-header')
    All Posts
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
        <div class="col-md-12">
            <ul class="posts">
                @foreach( $posts as $post )
                <li class="post_{{ $post->id }}">
                    <i class="fa fa-times del-post" data-del-post-id="{{ $post->id }}" aria-hidden="true"></i>
                    <div class="tbCell">
                        <strong>
                            <a href="{{ url('/edit-post/'.$post->id) }}" class="postTitle">{{ $post->title }}</a>
                        </strong>
                        <span class="pull-right shareBtn">
                            <a class="btn btn-success btn-sm" href="{{ url('/publish-post/'.$post->id) }}">Share on Social Media</a>
                        </span>
                        <p class="info hidden-xs">#{{ $post->id }} | Author {{ $user->name }} | published on blog: {{ $post->updated_at }}</p>
                    </div>
                </li>
                    @endforeach
            </ul>
        </div>
    </div>
@endsection