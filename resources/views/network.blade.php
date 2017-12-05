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

    <div class="flex-container-wrap mt20">
        @foreach($userAccounts as $item)
            @if(isset($item['userId']))
                <div class="item_accounts">
                    <a href="{{url($item["provider"].'/login')}}" class="accounts_block">
                        <img class="pull-left hidden-xs b2s-img-network" alt="{{$item['provider']}}" src="/soc_img/{{$item['icon']}}">
                        <div class="media-body network user_detalis_provider_{{ $item['userId'] }}">
                            <h3>Connected to {{ucfirst($item['provider'])}}</h3>
                            <span class="circle-green"></span>
                            <span class="success_text_green">online</span>
                            <span class="success_text_blue">( 1 / 1 )</span>
                            <span class="deleteAccount" data-id="{{$item['userId']}}" data-provider="{{$item['provider']}}" href="#">
                                <i class="fa fa-trash black" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </div>

            @else
                <div class="item_accounts">
                    <a href="{{url($item["provider"].'/login')}}" class="accounts_block">
                        <img class="pull-left hidden-xs b2s-img-network" alt="{{ $item['provider'] }}" src="/soc_img/{{ $item['icon'] }}">
                        <div class="media-body network">
                            <h3>Connect {{ucfirst($item['provider'])}}</h3>
                            <span class="circle-gray"></span>
                            <span class="grayText">offline</span>
                        </div>
                    </a>
                </div>
            @endif
        @endforeach
    </div>

@endsection

