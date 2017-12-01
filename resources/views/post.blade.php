@extends('layouts.master')
@section('title')
    {{ $post['title'] }}
@endsection
@section('page-header')
    Author {{ $user->name }} | published on blog: {{ $post->updated_at }}
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
            <form action="{{ url('/editPostAction/'.$post['id']) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="postTitle">Post Title</label>
                    <input type="text" id="postTitle" required="required" name="postTitle" class="form-control" placeholder="Title" value="{{ $post['title'] }}">
                </div>
                <div class="form-group">
                    <label for="image">Add Image (max 2 MB) image|mimes:jpeg,bmp,png</label>
                    <input type="file" id="image" name="image" class="form-control">
                    @if( $post['img'] != null )
                    <div class="w200px">
                        <i class="fa fa-times del-post-img" data-del-post-img-id="{{ $post['id'] }}" data-del-post-img-url="{{ $post['img'] }}" aria-hidden="true"></i>
                        <img src="{{ Storage::url($post['img']) }}" alt="{{ $post['title'] }}" width="100%">
                        <input type="hidden" value="{{ $post['img'] }}" name="postImgOldUrl">
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="postContent">Post Content</label>
                    <textarea class="mt10 form-control" rows="5" cols="50" placeholder="Write something about your post..." id="postContent" name="postContent" required="required">{{ $post['text'] }}</textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="Edit" class="form-control">
                </div>
            </form>
        </div>
    </div>
@endsection