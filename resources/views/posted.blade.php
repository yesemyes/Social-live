@extends('layouts.master')
@section('title')
    {{ $post['title'] }}
@endsection
@section('page-content')

    <p class="border_bottom">Author {{$user->name}} | @if($post['status']==1) Shared :@else Planning :@endif @if($post->schedule_date!=null) {{date('M d, Y', strtotime($post->schedule_date))}} {{date('h:i a', strtotime($post->schedule_date))}} @else {{date('M d, Y', strtotime($post->updated_at))}} {{date('h:i a', strtotime($post->updated_at))}} @endif
        @if($post->provider == 'instagram')
            <i class="f2em posRel t5 fa fa-{{$post->provider}}" aria-hidden="true" style="color: {{$post->icon}}"></i>
        @elseif($post->provider == 'facebook')
            <i class="f2em posRel t5 fa fa-{{$post->provider}}-official" aria-hidden="true" style="color: {{$post->icon}}"></i>
        @elseif($post->provider == 'google')
            <i class="f2em posRel t5 fa fa-{{$post->provider}}-plus-square" aria-hidden="true" style="color: {{$post->icon}}"></i>
        @else
            <i class="f2em posRel t5 fa fa-{{$post->provider}}-square" aria-hidden="true" style="color: {{$post->icon}}"></i>
        @endif
    </p>

    @if( Session::has('message_success') )
        <p class="msg_success">{{Session::get('message_success')}}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{Session::get('message_error')}}</p>
    @endif

    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('/editPostAction/'.$post['id']) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" id="post" name="posted" value="1">
                <input type="hidden" id="post_id" value="{{$post['id']}}">
                <input type="hidden" name="default_img" value="{{$post['img']}}">
                <div class="flex-container mt20 pl20 create_post">
                    <div class="flex-grow-2">
                        <label for="postTitle">Post Title</label>
                        <p class="postTitleWrapper">
                            <input type="text" id="postTitle" required="required" name="postTitle" class="postTitle" value="{{$post['title']}}" placeholder="Title">
                        </p>
                        <label for="postContent">Post Content</label>
                        <p class="postContentWrapper">
                            <textarea name="postContent" id="postContent" class="postContent" rows="10" required="required" placeholder="Content Goes Here">{{$post['text']}}</textarea>
                        </p>
                        {{--<button class="save_draft">Save Draft</button>--}}
                        <button class="publish_button">Update</button>
                        <button id="delete_post" class="delete_button">Delete</button>
                    </div>
                    <div class="flex-grow-1">
                        <label for="imgInp">Featured Image</label>
                        <p class="mt10">
                            <img id="blah" src="@if($post['img']!=null){{ Storage::url($post['img']) }}@else{{url('/img/file-image.png')}}@endif" style="border-radius: 4px;" alt="your image" width="150" height="auto" />
                        </p>
                        <p class="mt20">
                            <input type="file" id="imgInp" name="image" class="none">
                            <label class="choose_img" for="imgInp">Choose image</label>
                        </p>
                        @if($post['status']==0)
                            <div class="flex-container-wrap maxW200 mt10">
                                <div class='input-group date datetimepicker1'>
                                    <input type='text' name="calendar" id="calendar" value="{{$post['schedule_date']}}" />
                                    <span class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </span>
                                </div>
                                <input type="hidden" name="timezone" id="timezone">
                                <input type="hidden" name="schedule">
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection