@extends('layouts.master')

@section('title')
    Manage accounts
@endsection

@section('page-content')

    <h3 class="mt20 f16 border_bottom tUppercase">Manage accounts</h3>

    @if( Session::has('message_success') )
        <p class="msg_success">{{ Session::get('message_success') }}</p>
    @elseif( Session::has('message_error') )
        <p class="msg_error">{{ Session::get('message_error') }}</p>
    @endif

    <div class="flex-container-wrap">
        @foreach($userAccounts as $item)
            @if(isset($item['userId']))
                <div class="item_accounts">
                        <img class="pull-left hidden-xs b2s-img-network" alt="{{$item['provider']}}" src="/soc_img/{{$item['icon']}}">
                        <div class="media-body network">
                            <h4>{{$item['provider']}}
                                <span class="b2s-network-auth-count count_{{$item['userId']}}">(Connections <span class="">1</span>/1)</span>
                                <span class="pull-right"><a href="{{url($item["provider"].'/login')}}">+ Profile</a></span>
                            </h4>
                            <ul class="user_detalis_provider_{{ $item['userId'] }}">
                                <li>Profile: {{$item['first_name']}} {{$item['last_name']}} <span>(My profile)</span>
                                    <a href="{{url($item["provider"].'/login')}}" class="update_povider">
                                        <span class="glyphicon  glyphicon-refresh glyphicon-grey"></span>
                                    </a>
                                    <a class="deleteAccount" data-id="{{$item['userId']}}" href="#">
                                        <span class="glyphicon  glyphicon-trash glyphicon-grey"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                </div>

            @else
                <div class="item_accounts">
                        <img class="pull-left hidden-xs b2s-img-network" alt="{{ $item['provider'] }}" src="/soc_img/{{ $item['icon'] }}">
                        <div class="media-body network">
                            <h4>{{$item['provider']}}
                                <span class="b2s-network-auth-count">(Connections <span class="">0</span>/1)</span>
                                <span class="pull-right"><a href="{{url($item["provider"].'/login')}}">+ Profile</a></span>
                            </h4>
                        </div>
                </div>
            @endif
        @endforeach
    </div>

@endsection

