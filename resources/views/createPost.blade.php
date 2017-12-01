@extends('layouts.master')

@section('title')
    Create New Post
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

    <p class="border_bottom">Create New Post</p>

    <div class="row">
        <div class="col-lg-6">
            @if (count($errors) > 0)
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <form action="{{ url('/createPostAction') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="postTitle">Post Title</label>
                    <input type="text" id="postTitle" required="required" name="postTitle" class="form-control" placeholder="Title">
                </div>
                <div class="form-group">
                    <label for="image">Add Image</label>
                    <input type="file" id="image" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label for="postContent">Post Content</label>
                    <textarea class="mt10 form-control" rows="5" cols="50" placeholder="Write something about your post..." id="postContent" name="postContent" required="required"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="Create" class="form-control">
                </div>
            </form>
        </div>
    </div>
@endsection