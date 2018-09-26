<!-- Menu toggle button -->
<a href="#" class="dropdown-toggle notification-open" data-toggle="dropdown">
    <i class="fa fa-bell-o"></i>
    <span class="label label-warning" id="unread-count">{{count(Notification::unreadNotifications(10))}}</span>
</a>
<ul class="dropdown-menu box-shadow-as-border">
    @if(count(Notification::unreadNotifications(10)) > 0)
    <li class="header">Unread Notifications</li>
    @foreach(Notification::unreadNotifications(10) as $notification)
    <?php $notification->display_data = json_decode($notification->display_data); ?>
    <li>
        <!-- Inner Menu: contains the notifications -->
        <ul class="menu">
            <li class = "unread-notification "><!-- start notification -->
                @if($notification->display_data->type == 'leave_by_junior')
                <?php $link = url(config('laraadmin.adminRoute') . '/leave/teamMember'); ?>
                @elseif($notification->display_data->type == 'leave_action_by_senior')
                <?php $link = url(config('laraadmin.adminRoute') . '/leaves'); ?>
                @elseif($notification->display_data->type == 'timesheet_by_junior')
                <?php $link = url(config('laraadmin.adminRoute') . '/timesheet/teamMembers'); ?>
                @endif
                <a href="{{$link}}">
                    {{$notification->display_data->message}}
                </a>
            </li><!-- end notification -->
        </ul>
    </li>
    @endforeach
    @endif
    @if(count(Notification::readNotifications(10)) > 0)
    <li class="header">Read Notifications</li>
    @foreach(Notification::readNotifications(10) as $notification)
    <?php $notification->display_data = json_decode($notification->display_data); ?>
    <li>
        <!-- Inner Menu: contains the notifications -->
        <ul class="menu">
            <li class = "read-notification "><!-- start notification -->
                @if($notification->display_data->type == 'leave_by_junior')
                <?php $link = url(config('laraadmin.adminRoute') . '/leave/teamMember'); ?>
                @elseif($notification->display_data->type == 'leave_action_by_senior')
                <?php $link = url(config('laraadmin.adminRoute') . '/leaves'); ?>
                @elseif($notification->display_data->type == 'timesheet_by_junior')
                <?php $link = url(config('laraadmin.adminRoute') . '/timesheet/teamMembers'); ?>
                @endif
                <a href="{{$link}}">
                    {{$notification->display_data->message}}
                </a>
            </li><!-- end notification -->
        </ul>
    </li>
    @endforeach
    @endif
    <li class="footer"><a href="{{url('/showAllNotification')}}">View all</a></li>
</ul>