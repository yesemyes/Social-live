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

    <p class="border_bottom">Welcome <b>{{ Auth::user()->name }} !</b> @if(!$user->hasRole('owner')) (guest user) @endif</p>

    <h3 class="mt20 f16">All post</h3>

    <div class="flex-container mt20 pl20 border_bottom all_post_title">
        <div class="flex-grow-1">
            <p class="tUppercase">title</p>
        </div>
        <div class="status fb150">
            <p class="tUppercase">status</p>
        </div>
        <div class="action">
            <p class="tUppercase">action</p>
        </div>
    </div>

    @foreach( $posts as $post )
        <div class="flex-container mt20 pl20 border_bottom block_posts">
            <div class="fb200 flex-grow-1">
                <a href="{{ url('/edit-posted/'.$post->id) }}"
                   class="post_title_detalis @if( mobile_user_agent_switch()!="iphone" ) class-for-check-device-hover @endif posRel">{{$post->title}}</a>
            </div>
            @if($post->status==1)
                <div class="created">
                    <p class="created">Shared {{date('M d, Y', strtotime($post->updated_at))}}</p>
                </div>
                <div class="flex-grow-1 created_time">
                    <p class="time"> {{date('h:i a', strtotime($post->updated_at))}} </p>
                </div>
            @elseif($post->status==0)
                <div class="created">
                    <p class="created">Planning {{date("M d, Y", strtotime($post->schedule_date))}}</p>
                </div>
                <div class="flex-grow-1 created_time">
                    <p class="time"> {{date('h:i a', strtotime($post->schedule_date))}} </p>
                </div>
            @elseif($post->status==2)
                <div class="created">
                    <p class="created">Error on schedule sharing {{date("M d, Y", strtotime($post->schedule_date))}}</p>
                </div>
                <div class="flex-grow-1 created_time">
                    <p class="time"> {{date('h:i a', strtotime($post->schedule_date))}} </p>
                </div>
            @endif
            <div class="flex-grow-1 created_user">
                <p class="name">{{ $user->name }}</p>
            </div>
            <div class="success fb150">
                <span class="@if($post->status==1) circle-green @elseif($post->status==2) circle-red @else circle-yellow @endif"></span>
                <p class="@if($post->status==1) success_text_green @elseif($post->status==2) success_text_red @else success_text_yellow @endif">@if($post->status==1) Success @elseif($post->status==2) Error @else Pending @endif
                    @if($post->provider == 'instagram')
                        <i class="ml5 f2em fa fa-{{$post->provider}}" aria-hidden="true"
                           style="color: {{$post->icon}}"></i>
                    @elseif($post->provider == 'facebook')
                        <i class="ml5 f2em fa fa-{{$post->provider}}-official" aria-hidden="true"
                           style="color: {{$post->icon}}"></i>
                    @elseif($post->provider == 'google')
                        <i class="ml5 f2em fa fa-{{$post->provider}}-plus-square" aria-hidden="true"
                           style="color: {{$post->icon}}"></i>
                    @else
                        <i class="ml5 f2em fa fa-{{$post->provider}}-square" aria-hidden="true"
                           style="color: {{$post->icon}}"></i>
                    @endif
                </p>
            </div>
            <div class="share">
                <a href="{{ url('/publish-post/'.$post->id.'/posted') }}" class="share_text">SHARE</a>
            </div>
        </div>
    @endforeach
@endsection

