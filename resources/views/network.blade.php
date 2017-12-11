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
                        @if($item['provider'] == 'instagram')
                            <i class=" f3em fa fa-{{ $item['provider'] }}" aria-hidden="true" style="color: {{ $item['icon'] }}"></i>
                        @elseif($item['provider'] == 'facebook')
                            <i class=" f3em fa fa-{{ $item['provider'] }}-official" aria-hidden="true" style="color: {{ $item['icon'] }}"></i>
                        @elseif($item['provider'] == 'google')
                            <i class=" f3em fa fa-{{ $item['provider'] }}-plus-square" aria-hidden="true" style="color: {{ $item['icon'] }}"></i>
                        @else
                            <i class=" f3em fa fa-{{ $item['provider'] }}-square" aria-hidden="true" style="color: {{ $item['icon'] }}"></i>
                        @endif
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
                        @if($item['provider'] == 'instagram')
                            <i class=" f3em fa fa-{{ $item['provider'] }}" aria-hidden="true" style="color: #7F7C77"></i>
                        @elseif($item['provider'] == 'facebook')
                            <i class=" f3em fa fa-{{ $item['provider'] }}-official" aria-hidden="true" style="color: #7F7C77"></i>
                        @elseif($item['provider'] == 'google')
                            <i class=" f3em fa fa-{{ $item['provider'] }}-plus-square" aria-hidden="true" style="color: #7F7C77"></i>
                        @else
                            <i class=" f3em fa fa-{{ $item['provider'] }}-square" aria-hidden="true" style="color: #7F7C77"></i>
                        @endif
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

