@extends('la.layouts.app')

@section('htmlheader_title')
Project View
@endsection


@section('main-content')
<div id="page-content" class="profile2">
    <div class="bg-primary clearfix">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-3">
                        <!--<img class="profile-image" src="{{ asset('la-assets/img/avatar5.png') }}" alt="">-->
                    <div class="profile-icon text-primary"><i class="fa {{ $module->fa_icon }}"></i></div>
                </div>
                <div class="col-md-9">
                    <h4 class="name">{{ $project->$view_col }}</h4>
                    <div class="row stats">
                        <div class="col-md-4"><i class="fa fa-facebook"></i> 234</div>
                        <div class="col-md-4"><i class="fa fa-twitter"></i> 12</div>
                        <div class="col-md-4"><i class="fa fa-instagram"></i> 89</div>
                    </div>
                    <p class="desc">Test Description in one line</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dats1"><div class="label2">Admin</div></div>
            <div class="dats1"><i class="fa fa-envelope-o"></i> superadmin@gmail.com</div>
            <div class="dats1"><i class="fa fa-map-marker"></i> Pune, India</div>
        </div>
        <div class="col-md-4">
            <!--
            <div class="teamview">
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user1-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user2-160x160.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user3-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user4-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user5-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user6-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user7-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user8-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user5-128x128.jpg') }}" alt=""></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user6-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
                    <a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user7-128x128.jpg') }}" alt=""></a>
            </div>
            -->
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #1</span>
                    <small class="pull-right">20%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">20% Complete</span>
                    </div>
                </div>
            </div>
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #2</span>
                    <small class="pull-right">90%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 90%" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">90% Complete</span>
                    </div>
                </div>
            </div>
            <div class="dats1 pb">
                <div class="clearfix">
                    <span class="pull-left">Task #3</span>
                    <small class="pull-right">60%</small>
                </div>
                <div class="progress progress-xs active">
                    <div class="progress-bar progress-bar-warning progress-bar-striped" style="width: 60%" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                        <span class="sr-only">60% Complete</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1 actions">
            @la_access("Projects", "edit")
            <a href="{{ url(config('laraadmin.adminRoute') . '/projects/'.$project->id.'/edit') }}" class="btn btn-xs btn-edit btn-default"><i class="fa fa-pencil"></i></a><br>
            @endla_access

            @la_access("Projects", "delete")
            {{ Form::open(['route' => [config('laraadmin.adminRoute') . '.projects.destroy', $project->id], 'method' => 'delete', 'style'=>'display:inline']) }}
            <button class="btn btn-default btn-delete btn-xs" type="submit"><i class="fa fa-times"></i></button>
            {{ Form::close() }}
            @endla_access
        </div>
    </div>

    <ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
        <li class=""><a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}" data-toggle="tooltip" data-placement="right" title="Back to Projects"><i class="fa fa-chevron-left"></i></a></li>
        <li class="active"><a role="tab" data-toggle="tab" class="active" href="#tab-general-info" data-target="#tab-info"><i class="fa fa-bars"></i> General Info</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active fade in" id="tab-info">
            <div class="tab-content">
                <div class="panel infolist">
                    <div class="panel-default panel-heading">
                        <h4>General Info</h4>
                    </div>
                    <div class="panel-body">
                        @la_display($module, 'name')
                        @la_display($module, 'manager_id')
                        @la_display($module, 'lead_id')
                        @la_display($module, 'client_id')
                        @la_display($module, 'start_date')
                        @la_display($module, 'end_date')
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<h3 class="ml10">Sprint Details</h3>
<div class="box entry-form">
    <div class="box-body">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <table id="entry_table">
                    <thead class="entry-header">
                        <tr>
                            <th style="width: 40%;">Sprint Name<span class="required">*</span></th>
                            <th style="width: 20%;">Start Date<span class="required">*</span></th>
                            <th style="width: 20%;">End Date<span class="required">*</span></th>
                            <th style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="entry_body">
                        <tr class="entry-row">
                            <td>
                                <input class="form-control" placeholder="Enter Sprint Name" name="name" id="name" type="text" value="" required>
                            </td>
                            <td>
                                <div class="input-group date">
                                    <input class="form-control" placeholder="Start Date" required name="start_date" id="start_date" type="text" value="" autocomplete="off" required>
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group date">
                                    <input class="form-control" placeholder="End Date" required name="end_date" id="end_date" type="text" value="" autocomplete="off" required>
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-primary add-entry submit-form " data-value=''><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ url(config('laraadmin.adminRoute').'/sprintList') }}",
            method: "POST",
            data: {
                project_id: '{{ $project->id }}', _token: "{{ csrf_token()}}"
            }
        }).success(function (list) {
            $(list).each(function (key, item) {
                $('#entry_table').append("<tr class='recent-entry'>" +
                        "<td class='name'>" + item.name + "</td>" +
                        "<td class='start_date'>" + (new Date(item.start_date)).toShortFormat() + "</td>" +
                        "<td class='end_date'>" + (new Date(item.end_date)).toShortFormat() + "</td>" +
                        "<td>" +
                        '<button class="btn btn-success update-entry submit-form " data-value=' + item.id + '><i class="fa fa-edit"></i></button>' +
                        '<button class="btn btn-danger delete-entry submit-form " data-value=' + item.id + '><i class="fa fa-times"></i></button>' + "</td>" +
                        "</tr>");
            });
        });

        //form submition
        $(document).on('click', 'button.submit-form', function () {
            var send_data = {
                _token: "{{ csrf_token() }}",
                project_id: "{{ $project->id }}",
                name: $('#name').val(),
                start_date: dateFormatDB($('#start_date').val()),
                end_date: dateFormatDB($('#end_date').val()),
            };
            var saved_data = {
                _method: "POST",
                _token: "{{ csrf_token() }}",
                project_id: "{{ $project->id }}",
                name: $('#name').val(),
                start_date: dateFormatDB($('#start_date').val()),
                end_date: dateFormatDB($('#end_date').val()),
            };
            var el = $(this);

            if (el.hasClass('add-entry') || el.hasClass('update-entry-db')) {
                var url = "{{ url(config('laraadmin.adminRoute') . '/projects_sprints') }}";
                var method = "POST";
                if (el.hasClass('update-entry-db')) {
                    url = "{{ url(config('laraadmin.adminRoute') . '/projects_sprints') }}" + "/" + el.attr('data-value') + "?_method=PUT";
                    method = "PUT";
                }
                saved_data['_method'] = method;
                if (validateFields($('[required]'))) {
                    $('div.overlay').removeClass('hide');
                    $.ajax({
                        method: "POST",
                        url: "{{ url(config('laraadmin.adminRoute').'/checkProjectDates') }}",
                        data: {project_id: saved_data['project_id'], name: saved_data['name'], start_date: saved_data['start_date'], end_date: saved_data['end_date'], _token: "{{ csrf_token()}}"}
                    }).success(function (datesWithinRange) {
                        if (datesWithinRange === 'false') {
                            $('div.overlay').addClass('hide');
                            swal("Sprint dates should be within project dates!");
                            return false;
                        } else {
                            $.ajax({
                                method: "POST",
                                url: url,
                                data: send_data,
                                success: function (received) {
                                    if ($.isNumeric(received)) {
                                        update_row(saved_data, received);
                                        $('tr.entry-row').find('.submit-form').addClass('add-entry').removeClass('update-entry-db').attr('data-value', '');
                                        $('.add-entry').find('i').removeClass('fa-edit').addClass('fa-plus');
                                        $('div.overlay').addClass('hide');
                                    } else {
                                        $('div.overlay').addClass('hide');
                                        swal(received);
                                    }
                                }
                            });
                        }
                    });
                } else {
                    $('div.overlay').addClass('hide');
                    swal('Please fill all required fields!');
                }

            } else if (el.hasClass('update-entry')) {
                if ($('tr.entry-row button.submit-form').hasClass('update-entry-db')) {
                    $('div.overlay').addClass('hide');
                    swal('Submit last row first!');
                    return false;
                }
                show_update_row(el);
            } else if (el.hasClass('delete-entry')) {
                var parent_row = el.parents('tr.recent-entry');
                $('div.overlay').removeClass('hide');
                $.ajax({
                    method: 'POST',
                    url: "{{ url(config('laraadmin.adminRoute') . '/projects_sprints') }}" + "/" + el.attr('data-value'),
                    data: {_token: "{{ csrf_token() }}", id: el.attr('data-value'), ajax: true, _method: 'DELETE'},
                    success: function () {
                        $('div.overlay').addClass('hide');
                        parent_row.remove();
                        swal('Row deleted successfully!');
                    }
                });
            }

        });
    });
</script>
<script src="{{ asset('la-assets/js/pages/projects.js') }}"></script>
@endpush
