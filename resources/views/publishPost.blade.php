@extends('layouts.admin')
@section('title')
    Publish Post(s)
@endsection
@section('page-header')
    Publish Post(s)
@endsection
@section('page-content')
    @if( Session::has('message') )
        <div class="row">
            <div class="col-lg-9">
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
            <form action="{{ url('/publish-post') }}" method="POST" id="sharePost" enctype="multipart/form-data">
                {{ csrf_field() }}
                @if( $post['img'] != null )
                    <input type="hidden" name="postImage" value="{{ Storage::url($post['img']) }}">
                @endif
                <div class="b2s-post-area col-md-9 del-padding-left">
                    <div class="b2s-post-list">
                        @foreach($userAccounts as $key => $value)
                            @if( isset($value['userId']) )
                            <div class="b2s-post-item" data-network-name="{{ $value['provider'] }}">
                                <input type="hidden" data-soc="{{ $value['provider'] }}" name="access_token[]" value="{{ $value['access_token'] }}">
                                <input type="hidden" data-soc-sec="{{ $value['provider'] }}" name="access_token_secret[]" value="{{ $value['access_token_secret'] }}">
                                <div class="panel panel-group">
                                    <div class="panel-body  ">
                                        <div class="b2s-post-item-area">
                                            <div class="b2s-post-item-thumb hidden-xs">
                                                <img alt="soc icon" class="img-responsive b2s-post-item-network-image" src="/soc_img/{{ $value['icon'] }}">
                                            </div>
                                        </div>
                                        <div class="b2s-post-item-details">
                                            <h4 class="pull-left b2s-post-item-details-network-display-name">
                                               {{ $value['first_name'] }} {{ $value['last_name'] }}
                                            </h4>
                                            <div class="clearfix"></div>
                                            <p class="pull-left">Profile | {{ ucfirst($value['provider']) }}</p>

                                            <div class="clearfix"></div>
                                            @if( $value['provider'] == "reddit" )
                                            <div class="form-group">
                                                @if( isset($subreddits) && count($subreddits->data->children) > 0 && $subreddits != null )
                                                <select class="form-control b2s-select valid" name="subreddits_id">
                                                @foreach($subreddits->data->children as $key => $item)
                                                    <option value="{{$item->data->display_name}}">{{$item->data->display_name}}</option>
                                                @endforeach
                                                </select>
                                                @elseif( isset($subreddits) && count($subreddits->data->children) == 0 && $subreddits != null && $value['provider'] == "reddit" )
                                                    <div class="clearfix"></div>
                                                    <p style="color:red">Warning! You don't have any subscriptions</p>
                                                @else
                                                    <div class="clearfix"></div>
                                                    <p style="color:red">You are not authorized <a href="{{url($value['provider'].'/login')}}">reAuthorized</a></p>
                                                @endif
                                            </div>
                                            @endif

                                            @if( $value['provider'] == "pinterest" )
                                                <div class="form-group">
                                                    @if( isset($boards) && count($boards) > 0 && $boards != null )
                                                        <select class="form-control b2s-select valid" name="boards_id">
                                                            @foreach($boards as $key => $item)
                                                                <option value="{{$item->id}}">{{ $item->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <div class="clearfix"></div>
                                                        <p style="color:red">Warning! You have no boards</p>
                                                    @endif
                                                </div>
                                            @endif


                                            <div class="form-group">
                                                <label>Add Image (max 2 MB) image|mimes:jpeg,bmp,png</label>
                                                <input type="file" data-img="{{ $value['provider'] }}" name="images[]" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Link (url) @if($value['provider']=="linkedin" || $value['provider']=="reddit") * @endif</label>
                                                <input type="text" data-link="{{ $value['provider'] }}" name="url[]" placeholder="http://" class="form-control" @if($value['provider']=="linkedin" || $value['provider']=="reddit") required @endif>
                                            </div>
                                            <div class="form-group">
                                                <label>Content</label>
                                                <textarea data-post-title="{{ $value['provider'] }}" class="form-control tw-textarea-input" placeholder="Write something about your post..." name="postTitle[]" required="required">{{ $post->title }}</textarea>
                                                <input type="text" class="mt10 form-control" data-post-content="{{ $value['provider'] }}" style="width: 100%;" readonly name="postContent[]" value="{{ $post->text }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach

                    </div>
                </div>
                <div class="col-md-3">
                    @foreach($userAccounts as $value)
                        @if( isset($value['userId']) )
                            <div>{{ $value['provider'] }}
                                <input type="checkbox" name="connected[]" value="{{ $value['provider'] }}" checked="checked" class="connected"></div>
                            <hr>
                        @endif
                    @endforeach
                </div>
                <div class="b2s-publish-area">
                    <button class="btn btn-success pull-right btn-lg b2s-submit-btn">Share</button>
                </div>
            </form>
        </div>
    </div>
@endsection