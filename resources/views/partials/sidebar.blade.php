<div class="sidebar-wrapper">
    <div class="sidebar">
        <ul @if( mobile_user_agent_switch()!="iphone" ) class="class-for-check-device-hover" @endif>
            <li class="dashboard-item-menu"><a href="{{URL::to('/')}}" class="{{ Request::is('/') ? 'activeMenu' : '' }}"><i class="dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::to('/create-post')}}" class="{{ Request::is('create-post') ? 'activeMenu' : '' }}"><i class="create"></i> <span class="menu-span">Create Post</span></a></li>
            <li><a href="{{URL::to('/posts')}}" class="{{ Request::is('posts') ? 'activeMenu' : '' }}"><i class="posts"></i> <span class="menu-span">Posts</span></a></li>
            <li><a href="{{URL::to('/networks')}}" class="{{ Request::is('networks') ? 'activeMenu' : '' }}"><i class="accounts"></i> <span class="menu-span">Accounts</span></a></li>
            <li><a href="{{URL::to('/settings')}}" class="{{ Request::is('settings') ? 'activeMenu' : '' }}"><i class="settings"></i> <span class="menu-span">Settings</span></a></li>
        </ul>
    </div>
</div>