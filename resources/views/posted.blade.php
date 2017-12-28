@extends('layouts.master')
@section('title')
    {{ $post['title'] }}
@endsection
@section('page-content')

    <p class="border_bottom">Author {{ $user->name }} | published on: {{ $post->updated_at }}</p>

    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @endif

    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('/editPostAction/'.$post['id']) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="posted" value="1">
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
                    </div>
                    <div class="flex-grow-1">
                        <label for="imgInp">Featured Image</label>
                        <p class="mt10">
                            <img id="blah" src="@if($post['img']!=null){{ url($post['img']) }}@else{{url('/img/file-image.png')}}@endif" style="border-radius: 4px;" alt="your image" width="150" height="auto" />
                        <p class="mt20">
                            <input type="file" id="imgInp" name="image" class="none">
                            <label class="choose_img" for="imgInp">Choose image</label>
                        </p>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection