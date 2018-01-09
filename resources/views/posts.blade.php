@extends('layouts.master')
@section('title')
    Publish post(s)
@endsection
@section('page-content')

    <p class="border_bottom">Welcome <b>{{ Auth::user()->name }} !</b></p>

    @if( isset($userConnectedAccountsCount) && $userConnectedAccountsCount == 0 )
        <p class="accounts_check">You are not connected to any social account. <a href="{{url('/networks')}}" style="color: #0051F8;"><i class="fa fa-plus" aria-hidden="true"></i> Add Account</a></p>
    @endif

    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @endif

    <h3 class="mt20 f16">PUBLISH POST(s)</h3>

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
        <div class="flex-container mt20 pl20 border_bottom block_posts">
            <div class="fb200 flex-grow-1">
                <a href="{{ url('/edit-post/'.$post->id) }}" class="post_title_detalis @if( mobile_user_agent_switch()!="iphone" ) class-for-check-device-hover @endif posRel">{{$post->title}}</a>
            </div>
            <div class="flex-grow-1 created">
                <p class="created">Created {{date('M d, Y', strtotime($post->updated_at))}}</p>
            </div>
            <div class="flex-grow-1 created_time">
                <p class="time">{{date('H:i a', strtotime($post->updated_at))}}</p>
            </div>
            <div class="flex-grow-1 created_user">
                <p class="name">{{ $user->name }}</p>
            </div>
            <div class="success">
                <span class="circle-blue"></span>
                <p class="success_text_blue">Published</p>
            </div>
            <div class="share">
                <a href="@if($userConnectedAccountsCount != 0) {{ url('/publish-post/'.$post->id) }} @else # @endif" class="share_text">SHARE</a>
            </div>
        </div>
    @endforeach

    {{--<div class="row">
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
    </div>--}}
@endsection