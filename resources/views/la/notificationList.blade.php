@extends('la.layouts.app')

@section('htmlheader_title') Notifications @endsection
@section('contentheader_title') Notifications @endsection
@section('contentheader_description')@endsection

@section('main-content')
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    @if(count(Notification::unreadNotifications()) > 0)
    <div class="row">
        <ul class="list-style-none p0 notification-page-main-ul">
            <li class="header">Unread Notifications</li>
            @foreach(Notification::unreadNotifications() as $notification)
            <?php $notification->display_data = json_decode($notification->display_data); ?>
            <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu page-notification-ul">
                    <li class = ""><!-- start notification -->
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
        </ul>
    </div><!-- /.row -->
    @endif
    
    @if(count(Notification::readNotifications()) > 0)
    <div class="row">
        <ul class="list-style-none p0 notification-page-main-ul">
            <li class="header">Read Notifications</li>
            @foreach(Notification::readNotifications() as $notification)
            <?php $notification->display_data = json_decode($notification->display_data); ?>
            <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu page-notification-ul">
                    <li class = ""><!-- start notification -->
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
        </ul>
    </div><!-- /.row -->
    @endif
    
    @if((count(Notification::readNotifications()) == 0) && (count(Notification::unreadNotifications()) == 0))
    <div class="row">
        No notifications received.
    </div><!-- /.row -->
    @endif
    
</section><!-- /.content -->
@endsection

@push('scripts')
<script>

</script>
@endpush