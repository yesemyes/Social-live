@extends('layouts.master')

@section('title')
    Create New Post
@endsection

@section('page-content')

    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @endif

    <p class="border_bottom">Create New Post</p>

    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif


    <form action="{{ url('/createPostAction') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="flex-container mt20 pl20 create_post">
            <div class="flex-grow-2">
                <label for="postTitle">Post Title</label>
                <p class="postTitleWrapper"><input type="text" id="postTitle" required="required" name="postTitle" class="postTitle" placeholder="Title"></p>
                <label for="postContent">Post Content</label>
                <p class="postContentWrapper"><textarea name="postContent" id="postContent" class="postContent" rows="10" required="required" placeholder="Content Goes Here"></textarea></p>
                <button class="save_draft">Save Draft</button>
                <button class="publish_button">Publish</button>
            </div>
            <div class="flex-grow-1">
                <label for="imgInp">Featured Image</label>
                <p class="mt10">
                    <img id="blah" src="{{url('/img/file-image.png')}}" style="border-radius: 4px;" alt="your image" width="150" height="auto" />
                    <p class="mt10">
                        <input type="file" id="imgInp" name="image" class="none">
                        <label class="choose_img" for="imgInp">Choose image</label>
                    </p>
                </p>
            </div>
        </div>
    </form>
@endsection