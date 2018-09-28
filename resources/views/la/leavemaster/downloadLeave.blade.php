@extends("la.layouts.app")
<style>
    div.overlay{
        background: none repeat scroll 0 0 #00000026;
        position: absolute;
        display: block;
        z-index: 1000001;
        top: 0;
        height: 100%;
        width: 100%;
        margin-top: 50px;
    }
    .loader {
        position: relative;
        border: 8px solid #7b7b7b;
        border-top: 8px solid #fbfbfb;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        top: 280px;
        left: 520px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


    input[type="search"].form-control.input-sm{
        float : right;
        margin:5px;
        border:none;
        border-bottom: 1px solid #9a9999;
        font-weight: 400;
    }

</style>
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/leaves') }}">Leaves</a> :
@endsection
@section("section", "Leaves")
@section("section_url", url(config('laraadmin.adminRoute') . '/leaves'))


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
            <div class="col-md-10 col-md-offset-1">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="start_date">From* :</label>
                        <div class="input-group date">
                            <input class="form-control valid" placeholder="Enter Start Date" required="1" name="start_date" type="text" autocomplete="off" aria-required="true" aria-invalid="false">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="start_date">To* :</label>
                        <div class="input-group date">
                            <input class="form-control valid" placeholder="Enter End Date" required="1" name="end_date" type="text" autocomplete="off" aria-required="true" aria-invalid="false">
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <button class="btn btn-success" name="export" id="export">Export Leave Records</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $(document).on('click', '#export', function () {
        $.ajax({
            method: "POST",
            url: "{{ url('/exportLeaveToAuthority') }}",
            data: {start_date: $('input[name="start_date"]').val(), end_date: $('input[name="end_date"]').val(), _token: "{{ csrf_token()}}"}
        }).success(function (response) {
            var a = document.createElement("a");
            a.href = response.file;
            a.download = response.name;
            document.body.appendChild(a);
            a.click();
            a.remove();
        });
    });

//    $('.date').data("DateTimePicker").minDate(moment().subtract(1, 'days').millisecond(0).second(0).minute(0).hour(0));
//    $('.date').data("DateTimePicker").maxDate(moment()).daysOfWeekDisabled([0, 6]);
});
</script>
@endpush
