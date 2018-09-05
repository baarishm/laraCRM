@extends("la.layouts.app")
@section("contentheader_title")
Team Leave Dashboard
@endsection
@section("main-content")


<div class="">
    <br />
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p>{{ \Session::get('success') }}</p>
    </div><br />

    @endif
    <div class="card" style="background: #FFF">
        @if(!empty($leaveMaster))
        <button type="button" class="days btn" id="P"  onclick="DateSorting('P')">Previous Day</button>
        <button type="button" class="days btn" id="T"   onclick="DateSorting('T')">Today</button>
        <button type="button" class="days btn" id="N" onclick="DateSorting('N')">Next Day</button>
        <input type="text" readonly="true" id="holder" class="pull-right" style="border:none;">
        <table class="table table-striped table-bordered"  id="searchdate">


            <tr>
            <thead>
            <th>Name</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>No Of Days</th>
            <th>Leave Type</th>
            <th>Purpose</th>
            <th style="width:155px; text-align:center;">Action</th>
            </thead>
            </tr>

            <tbody>

                @foreach($leaveMaster as $leaveMasterRow)
                @php
                $FromDate=date('d M Y', strtotime($leaveMasterRow->FromDate));
                $ToDate=date('d M Y', strtotime($leaveMasterRow->ToDate));
                $Approved=$leaveMasterRow->Approved;
                @endphp

                <tr>

                    <td>{{$leaveMasterRow->Employees_name}}</td>

                    <td>{{$FromDate}}</td>
                    <td>{{$ToDate}}</td>
                    <td>{{$leaveMasterRow->NoOfDays}}</td>
                    <td>{{(($leaveMasterRow->leave_name != '')? $leaveMasterRow->leave_name : "Not Specified" ) }}</td> 

<!--                    <td>{{$leaveMasterRow->LeaveReason}}</td>-->
                    <td><span  id="btn2" data-toggle="popover"  title="{{$leaveMasterRow->LeaveReason}}" data-content="Default popover">Leave Reason ..</span>

                    </td>
                     <!--<td>{{(($leaveMasterRow->Approved != '')? $leaveMasterRow->Approved : 'Pending' ) }}</td>-->
                    <td class="text-center"> 
                        @if($Approved=='1')
                       
                        <span class="text-success">Approved</span>
                            
                       
                        @elseif($Approved=='0')
                    
                        <span class="text-danger">Rejected</span>
                    
                        @else
                       
                        <div class="">
                            @if($Approved=='0' || $Approved=='')
                            <button type="button" class="btn btn-success" name="Approved" id="Approved" data-id = <?php echo $leaveMasterRow->id; ?> onclick="myfunction(this);" data-days='{{{{$leaveMasterRow->NoOfDays}}}}'>Approve</button>
                            @endif
                            @if($Approved=='1' || $Approved=='')
                            <button type="button" class="btn btn" name="Rejected" id="Rejected" data-id = <?php echo $leaveMasterRow->id; ?> onclick="myfunction(this);" style="background-color: #f55753;border-color: #f43f3b;color: white" data-days='{{{{$leaveMasterRow->NoOfDays}}}}'>Reject</button> 
                            @endif
                        </div>
                      
                        @endif


                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div>No Record found!</div>
        @endif
    </div>



    @endsection
    @push('scripts')
    <script type="text/javascript">
        function DateSorting(date) {

            $.ajax({
                url: "{{ url(config('laraadmin.adminRoute') . '/datesearch') }}",
                type: 'POST',
                data: {'date': date, _token: "{{ csrf_token() }}"},
                success: function (data) {
                    data = $.parseJSON(data);
                    $("#searchdate tbody").html(data.html);
                    $(".days").removeClass('btn-success');
                    $("#" + data.day).addClass('btn-success');
                    if (data.day == 'T') {
                        $('#N').hide();
                    } else {
                        $('#N').show();
                    }
                }
            });
        }


        function myfunction(button)
        {

            var controlid = $(button).attr('id');

            var approved = 0;
            if (controlid == 'Approved')
            {
                approved = 1;
               

            }
            $.ajax({
                url: "{{ url('/approveLeave') }}",
                type: 'GET',
                data: {'approved': approved, 'id': $(button).attr('data-id'), 'days' : $(button).attr('data-days')},
                success: function (data) {
                    swal('Application has been successfully ' + ((approved) ? 'Approved' : 'Rejected') + '!');
               
                }
            });
            var vid = $(button).attr('data-id');
            $(button).parent('td').html((approved) ? '<span class="text-success">Approved</span>' : '<span class="text-danger">Rejected</span>');
            $('[data-id=' + vid + ']').remove();
        }
        
        $(function () {
            $('.days').click(function () {
                var elem_id = $(this).attr('id');
                var add_day = ((elem_id == 'N') ? 1 : ((elem_id == 'T') ? 0 : -1));
                var today = new Date();
                var dd = today.getDate() + add_day;
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm;
                }
                var today = yyyy + '-' + mm + '-' + dd;

                $('#holder').val(new Date(today).toShortFormat());
            });
        });

    </script>

    @endpush

