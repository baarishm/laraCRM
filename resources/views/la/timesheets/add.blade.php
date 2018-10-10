@extends("la.layouts.app")

@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/timesheets') }}">Add Timesheet Entry</a>
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
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
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
                                    <input class="form-control" placeholder="Enter Date" required name="date" id="date" type="text" value="" autocomplete="off">
                                    <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <select class="form-control" name="project_id" id="project_id" required >
                                    @foreach($projects as $project)
                                    <option data-name="{{$project->name}}" value="{{$project->id}}">{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="projects_sprint_id" id="projects_sprint_id" required>
                                    @foreach($projects_sprints as $projects_sprint)
                                    <option data-name="{{$projects_sprint->name}}" value="{{$projects_sprint->id}}">{{$projects_sprint->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="task_id" id="task_id" required data-url="{{ url('/isLeave') }}">
                                    @foreach($tasks as $task)
                                    <option data-name="{{$task->name}}" value="{{$task->task_id}}">{{$task->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input class="form-control" placeholder="Enter Description" name="comments" id="comments" type="text" value="" maxlength="250">
                            </td>
                            <td>
                                <select class="form-control" name="hours" id="hours" required>
                                    @for($i = 0; $i <= 24 ; $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <select class="form-control" name="minutes" id="minutes" required>
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary add-entry submit-form add-entry-form" data-value=''><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="submitor_id" id="submitor_id" value="<?php echo base64_encode(base64_encode(Auth::user()->context_id)); ?>" />
@endsection

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('la-assets/js/pages/timesheets.js') }}"></script>
@endpush
