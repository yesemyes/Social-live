@extends('layouts.master')
@section('title')
    Publish Post(s)
@endsection
@section('page-content')
    @if( Session::has('share_message_result') )
        @foreach(session()->get('share_message_result') as $item)
            <p class="success_share_status">{{$item}}</p>
        @endforeach
    @endif
    @if( Session::has('schedule_message_result') )
        @foreach(session()->get('schedule_message_result') as $item)
            @if(isset($item['provider'])&&$item['provider']!=null)
                <p class="success_share_status">Success! Your scheduled post will be shared on {{$item['provider']}} on {{$item['datetime']}}</p>
            @else
                <p class="msg_error">{{$item}}</p>
            @endif
        @endforeach
    @endif
    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @elseif( Session::has('message_error_ins') )
        <p class="msg_error">{{ Session::get('message_error_ins') }}</p>
    @elseif( isset($userConnectedAccountsCount) && $userConnectedAccountsCount == 0 )
        <p class="accounts_check">You are not connected to any social account. <a href="{{url('/networks')}}" style="color: #0051F8;"><i class="fa fa-plus" aria-hidden="true"></i> Add Account</a></p>
    @endif
    <form action="{{ url('/publish-post') }}" method="POST" id="sharePost" enctype="multipart/form-data" class="">
        {{ csrf_field() }}
        <div class="mt20 border_bottom flex-container">
            <div class="f16 share_posts_header_left"><b>PUBLISH POST(s)</b></div>
            <div class="share_posts_header_right flex-container-wrap space-between">
                <div class="dIBlock top_social_button mLAuto">
                    <span class="mr10 f12pt">Share to this accounts</span>
                    <div class="social_icons_size dIBlock">
                        @foreach($userAccounts as $key => $value)
                            @if( isset($value['userId']) )
                                <input type="checkbox" name="connected[]" value="{{ $value['provider'] }}" id="soc-{{$value['provider']}}" checked="checked" class="connected">
                                @if($value['provider'] == 'instagram')
                                    <label for="soc-{{$value['provider']}}"><i class="soc-icon-cursor fa fa-{{$value['provider']}}" aria-hidden="true" style="color: {{ $value['icon'] }}"></i></label>
                                @elseif($value['provider'] == 'facebook')
                                    <label for="soc-{{$value['provider']}}"><i class="soc-icon-cursor fa fa-{{ $value['provider'] }}-official" aria-hidden="true" style="color: {{ $value['icon'] }}"></i></label>
                                @elseif($value['provider'] == 'google')
                                    <label for="soc-{{$value['provider']}}"><i class="soc-icon-cursor fa fa-{{$value['provider']}}-plus-square" aria-hidden="true" style="color: {{ $value['icon'] }}"></i></label>
                                @else
                                    <label for="soc-{{$value['provider']}}"><i class="soc-icon-cursor fa fa-{{$value['provider']}}-square" aria-hidden="true" style="color: {{ $value['icon'] }}"></i></label>
                                @endif
                            @endif
                        @endforeach
                        @role('owner')
                            @foreach($userAccounts as $value)
                                @if( !isset($value['userId']) )
                                    <input type="checkbox" id="{{ $value['provider'] }}" name="connected[]" value="{{ $value['provider'] }}" disabled>
                                    @if($value['provider'] == 'instagram')
                                        <a href="#" id="myBtn"><i class="fa fa-{{ $value['provider'] }}" aria-hidden="true" style="color: #9A9691"></i></a>
                                    @elseif($value['provider'] == 'facebook')
                                        <a href="{{url('/'.$value['provider'].'/login')}}"><i class="fa fa-{{ $value['provider'] }}-official" aria-hidden="true" style="color: #9A9691"></i></a>
                                    @elseif($value['provider'] == 'google')
                                        <a href="{{url('/'.$value['provider'].'/login')}}"><i class="fa fa-{{ $value['provider'] }}-plus-square" aria-hidden="true" style="color: #9A9691"></i></a>
                                    @else
                                        <a href="{{url('/'.$value['provider'].'/login')}}"><i class="fa fa-{{ $value['provider'] }}-square" aria-hidden="true" style="color: #9A9691"></i></a>
                                    @endif
                                @endif
                            @endforeach
                        @endrole
                    </div>
                </div>
                @if( isset($userConnectedAccountsCount) && $userConnectedAccountsCount > 0 )
                    <div class="mLAuto top_share_button">
                        <button class="share_button_color"><i class="fa fa-share-alt" aria-hidden="true"></i> <b>SHARE NOW</b></button>
                        <button class="schedule_button_color" id="myBtn_schedule"><i class="fa fa-clock-o" aria-hidden="true"></i> <b>SCHEDULE</b></button>
                    </div>
                @endif
            </div>
        </div>
        <div class="flex-container-wrap space-between">
            @if( $post['img'] != null )
                <input type="hidden" name="postImage" value="{{ Storage::url($post['img']) }}">
            @endif
			  <?php $checkConnected = [];?>
            @foreach($userAccounts as $key => $value)
                @if( isset($value['userId']) )
					  <?php array_push($checkConnected,$value['userId']);?>
                    <input type="hidden" data-soc="{{ $value['provider'] }}" name="access_token[]" value="{{ $value['access_token'] }}">
                    @if($value['provider'] == "instagram")
                        <input type="hidden" name="username" value="{{ $value['first_name'] }}">
                        <input type="hidden" name="password" value="{{ $value['access_token'] }}">
                    @endif
                    <input type="hidden" data-soc-sec="{{ $value['provider'] }}" name="access_token_secret[]" value="{{ $value['access_token_secret'] }}">
                    <input type="hidden" data-soc-id="{{ $value['provider'] }}" name="prov_user_id[]" value="{{ $value['provUserId'] }}">
                    <div class="publishBlock" data-network-name="{{$value['provider']}}">
                        <div class="block-content">
                            <div class="flex-container">
                                <div class="soc-logo">
                                    @if($value['provider'] == 'instagram')
                                        <i class=" f3em fa fa-{{ $value['provider'] }}" aria-hidden="true" style="color: {{ $value['icon'] }}"></i>
                                    @elseif($value['provider'] == 'facebook')
                                        <i class=" f3em fa fa-{{ $value['provider'] }}-official" aria-hidden="true" style="color: {{ $value['icon'] }}"></i>
                                    @elseif($value['provider'] == 'google')
                                        <i class=" f3em fa fa-{{ $value['provider'] }}-plus-square" aria-hidden="true" style="color: {{ $value['icon'] }}"></i>
                                    @else
                                        <i class=" f3em fa fa-{{ $value['provider'] }}-square" aria-hidden="true" style="color: {{ $value['icon'] }}"></i>
                                    @endif
                                </div>
                                <div class="soc-info-wrap">
                                    <p class="soc-info f10pt">{{ $value['first_name'] }} {{ $value['last_name'] }}</p>
                                    @if( $value['provider'] == "pinterest" )
                                        <div class="mt5">
                                            @if( isset($boards) && count($boards) > 0 && $boards != null )
                                                <select class="input-select" name="boards_id" data-cat="pinterest">
                                                    @foreach($boards as $key => $item)
                                                        <option value="{{$item->id}}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <p style="color:red" class="f9pt">Warning! You have no boards</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if( $value['provider'] == "reddit" )
                                        <div class="mt5">
                                            @if( isset($subreddits) && count($subreddits->data->children) > 0 && $subreddits != null )
                                                <select class="input-select" name="subreddits_id" data-cat="reddit">
                                                    @foreach($subreddits->data->children as $key => $item)
                                                        <option value="{{$item->data->display_name}}">{{$item->data->display_name}}</option>
                                                    @endforeach
                                                </select>
                                            @elseif( isset($subreddits) && count($subreddits->data->children) == 0 && $subreddits != null && $value['provider'] == "reddit" )
                                                <p style="color:red" class="f9pt">Warning! You don't have any subscriptions</p>
                                            @else
                                                <p style="color:red" class="f9pt">You are not authorized <a href="{{url($value['provider'].'/login')}}" class="reconnect-color">reAuthorized</a></p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt10 f11pt"><label for="title{{ $value['provider'] }}">Title</label></div>
                            <div class="mt5"><input type="text" class="input-title" id="title{{ $value['provider'] }}" data-post-title="{{$value['provider']}}" name="postTitle[]" value="{{$post->title}}"></div>
                            <div class="mt10 f11pt"><label for="content{{ $value['provider'] }}">Content</label></div>
                            <div class="mt5"><textarea data-post-content="{{ $value['provider'] }}" rows="6" id="content{{ $value['provider'] }}" class="input-content" placeholder="Write something about your post..." name="postContent[]" required="required">{{$post->text}}</textarea></div>
                            <div class="mt10 f11pt"><label for="link{{ $value['provider'] }}">Link ( url )</label></div>
                            <div class="mt5"><input type="text" class="input-title" id="link{{ $value['provider'] }}" data-link="{{$value['provider']}}" name="url[]" @if($value['provider']=="linkedin" || $value['provider']=="reddit") required @endif placeholder="http(s)://"></div>
                            <div class="mt10">
                                <div class="flex-container">
                                    <div class="fb35">
                                        <input type="file" data-img="{{ $value['provider'] }}" id="imgInp{{$value['provider']}}" name="images[]" class="none">
                                        <label class="dIBlock choose_img f10pt" id="ablah-{{$value['provider']}}" for="imgInp{{$value['provider']}}">Choose image</label>
                                    </div>
                                    <div class="fb65 f8pt">
                                        <p class="pl10">Featured Image</p>
                                        <p class="pl10">2mb max size, jpg,bmp or png</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt10 tCenter">
                                <img id="blah-{{$value['provider']}}" src="#" class="none" style="border-radius: 4px;" alt="your image" width="200" height="auto" />
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @if( isset($userConnectedAccountsCount) && $userConnectedAccountsCount > 0 )
            <div class="flex-container flex-end">
                <div class="block-content">
                    <button class="share_button_color p10"><i class="fa fa-share-alt" aria-hidden="true"></i> <b class="pl5">SHARE NOW</b></button>
                </div>
            </div>
            <div id="myModalSchedule" class="modal_schedule">
                <div class="modal-content-schedule">
                    <div class="schedule-header">
                        <span class="close_schedule">&times;</span>
                        <h3 class="f14px">Schedule Post</h3>
                    </div>
                    <div class="schedule-content">
                        <div class="f14px">Select a date and time in the future for when you want your post to publish.</div>
                        <div class="flex-container mt10">
                            <div class='input-group date datetimepicker1 mAuto'>
                                <input type='text' name="calendar" id="calendar" />
                                <span class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </span>
                            </div>
                            <input type="hidden" name="timezone" id="timezone">
                        </div>
                    </div>
                    <div class="schedule-footer flex-container">
                        <div class="mLAuto">
                            <button class="disable_schedule">Cancel</button>
                            <button name="schedule_posts" class="schedule_send_button">Schedule</button>
                        </div>
                        <div class="schedule-error"></div>
                    </div>
                </div>
            </div>
        @endif
    </form>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p class="tCenter">Instagram</p>
            <div class="ins-content">
                <p class="mt5"><input type="text" id="ins-username" name="username" value="" placeholder="Username or Email" required="required" autocomplete="off"></p>
                <p class="mt5"><input type="password" id="ins-password" name="password" value="" placeholder="Password" required="required" autocomplete="off"></p>
                <p><button id="ins-form">Login</button></p>
            </div>
            <div class="ins-error"></div>
        </div>
    </div>
    <div class="loader"></div>
@endsection
@section('myjsfile')

    {{--<link href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">--}}
    {{--<script src="{{ url('dist/js/moment.min.js') }}"></script>
    <script src="{{ url('dist/js/bootstrap-datetimepicker.min.js') }}"></script>--}}
    <script type="text/javascript">
        /*var hours = new Date().getHours();
        var hours = (hours+24)%24;
        var mid='AM';
        if(hours==0){hours=12;}
        else if(hours>12)
        {
            hours=hours%12;
            mid='PM';
        }
        var time = hours + ":" + new Date().getMinutes() + " " + mid;*/
        $(function () {
            /*$('.datetimepicker1').datetimepicker({
                defaultDate: new Date(),
                format: 'MM/DD/YYYY'
            });
            $('.datetimepicker2').datetimepicker({
                format: 'LT',
                icons:{
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                }
            });*/
            $('.datetimepicker1').datetimepicker({
                inline: true,
                sideBySide: true,
                icons: {
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down"
                }
            });
        });
    </script>
@endsection