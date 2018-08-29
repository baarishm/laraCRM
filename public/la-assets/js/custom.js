$('document').ready(function () {

    $('#date_search').datetimepicker({
        format: 'Y-MM-DD',
        minDate: moment('2016-08-29')
    });
    if ($('.date').length > 0) {
        $('.date').each(function () {
            $(this).data('DateTimePicker').format('DD MMM YYYY').widgetPositioning({vertical: 'top'});
            var date = new Date();
            var child_input = $(this).find('input');
            if (child_input.val() != '') {
                date = new Date(child_input.val());
            }
            $(this).data('DateTimePicker').date(date).useStrict(true).keepInvalid(true);
            $(this).on('paste keydown', function (e) {
                e.preventDefault();
                return false;
            });
            if (child_input.attr('name') == 'start_date' || child_input.attr('name') == 'end_date') {
                $(this).on('dp.change', function (e) {
                    if (new Date($('form input[name="start_date"]').val()) > new Date($('form input[name="end_date"]').val())) {
                        swal('Start date has to be smaller than end date!');
                        child_input.val('');
                    }
                });
            }
        });
        $('.date>input').prop('autocomplete', 'off');
    }

    if ($('.datepicker').length > 0) {
        $(".datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',

        });
    }

    if ($('.cancel-button').length > 0) {
        $('.cancel-button').click(function (e) {
            e.preventDefault();
            window.location.href = $(this).find('a').attr('href');
        });
    }
    setTimeout(function () {
        if ($('form.delete').length > 0) {
            $('div.overlay').removeClass('hide');
            $("form.delete").each(function () {
                var form = $(this);
                form.find('button[type="submit"]').on("click", function (e) {
                    e.preventDefault();
                    if (confirm("Are you sure to delete?")) {
                        form.submit();
                    } else {
                        return false;
                    }
                    // swal({
                    // title: "Are you sure?",
                    // text: "You will not be able to recover this action!",
                    // type: "warning",
                    // showCancelButton: true,
                    // confirmButtonClass: "btn-danger",
                    // confirmButtonText: "Delete",
                    // cancelButtonText: "Cancel",
                    // closeOnConfirm: false,
                    // closeOnCancel: false
                    // },
                    // function(isConfirm) {
                    // console.log(isConfirm);
                    // if(isConfirm){
                    // form.submit();
                    // }
                    // });
                });
            });

            $('div.overlay').addClass('hide');
        }
    }, 1500);
});


Date.prototype.toShortFormat = function () {

    var month_names = ["Jan", "Feb", "Mar",
        "Apr", "May", "Jun",
        "Jul", "Aug", "Sep",
        "Oct", "Nov", "Dec"];

    var day = this.getDate();
    var month_index = this.getMonth();
    var year = this.getFullYear();

    return "" + day + " " + month_names[month_index] + " " + year;
}