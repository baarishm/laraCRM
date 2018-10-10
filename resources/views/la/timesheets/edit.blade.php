@extends("la.layouts.app")
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Edit Timesheet Entry</a>
@endsection

@section("section", "Timesheets")
@section("section_url", url(config('laraadmin.adminRoute') . '/timesheets'))


@section("main-content")
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="box entry-form">
    <div class="box-header">

    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                {!! Form::model($timesheet, ['route' => [config('laraadmin.adminRoute') . '.timesheets.update', $timesheet->id ], 'method'=>'PUT', 'id' => 'timesheet-edit-form']) !!}

                <table id="entry_table" data-url = "{{url(config('laraadmin.adminRoute'))}}" data-workHours = "{{ url('/hoursWorked') }}">
                    <thead class="entry-header">
                        <tr>
                            <th style="width: 16%;">Date<span class="required">*</span></th>
                            <th style="width:15%;">Project<span class="required">*</span></th>
                            <th style="width:15%;">Sprint<span class="required">*</span></th>
                            <th style="width:15%;">Task<span class="required">*</span></th>
                            <th><span class="required hide description">*</span>Description</th>
                            <th>Hours<span class="required">*</span></th>
                            <th>Minutes<span class="required">*</span></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="entry_body">
                        <tr class="entry-row">
                            <td>
                                <div class="input-group date">
                                    <input class="form-control" placeholder="Enter Date" required name="date" id="date" type="text" value="{{$module->row->date}}" autocomplete="off">
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select class="form-control" name="project_id" id="project_id" required>
                                    @foreach($projects as $project)
                                    <option data-name="{{$project->name}}" value="{{$project->id}}" <?php echo (($project->id == $module->row->project_id) ? 'selected' : ''); ?>>{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="projects_sprint_id" id="projects_sprint_id" required>
                                    @foreach($projects_sprints as $projects_sprint)
                                    <option data-name="{{$projects_sprint->name}}" value="{{$projects_sprint->id}}" <?php echo (($projects_sprint->id == $module->row->projects_sprint_id) ? 'selected' : ''); ?>>{{$projects_sprint->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="task_id" id="task_id">
                                    @foreach($tasks as $task)
                                    <option value="{{$task->task_id}}" <?php echo (($task->task_id == $module->row->task_id) ? 'selected' : ''); ?>>{{$task->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control" placeholder="Enter Description" name="comments" id="comments" type="text" value="{{$module->row->comments}}" maxlength="250">
                            </td>
                            <td>
                                <select class="form-control" name="hours" id="hours" required>
                                    @for($i = 0; $i <= 24 ; $i++)
                                    <option value="{{$i}}"  <?php echo (($i == $module->row->hours) ? 'selected' : ''); ?>>{{$i}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="minutes" id="minutes" required>
                                    <option value="00"  <?php echo (('00' == $module->row->minutes) ? 'selected' : ''); ?>>00</option>
                                    <option value="30"  <?php echo (('30' == $module->row->minutes) ? 'selected' : ''); ?>>30</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary add-entry submit-form" data-value=''><i class="fa fa-edit"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <input type="hidden" name="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />
                <input type="hidden" id="timesheet_id" value="{{ $timesheet -> id}}" />
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('la-assets/js/pages/timesheets.js') }}"></script>
@endpush
