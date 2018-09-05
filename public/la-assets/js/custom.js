$('document').ready(function () {
    $('div.overlay').addClass('hide');
    $('#date_search').datetimepicker({
        format: 'DD MMM YYYY',
        minDate: moment('2016-08-29')
    });
    if ($('.date').length > 0) {
        $('.date').each(function () {
            $(this).on('dp.show dp.update', function () {
                $(".datepicker-years .picker-switch").removeAttr('title')
                        .css('cursor', 'default')
                        .css('background', 'inherit')
                        .on('click', function (e) {
                            e.stopPropagation();
                        });
            });
            $(this).data('DateTimePicker').format('DD MMM YYYY').widgetPositioning({vertical: 'auto'});
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
                    var start_date = $('input[name="start_date"]').val();
                    if (start_date != '') {
                        $('[name="end_date"]').parents('.date').data('DateTimePicker').minDate(moment(new Date(start_date))).date(start_date);
                    }
                    if (new Date($('input[name="start_date"]').val()) > new Date($('input[name="end_date"]').val())) {
                        $('input[name="end_date"]').val('');
                    }
                });
            }
        });
        $('.date>input').prop('autocomplete', 'off');
        if ($('[name="date_birth"]').length > 0)
            $('[name="date_birth"]').parents('.date').data('DateTimePicker').minDate(moment().subtract(70, 'years')).maxDate(moment().subtract(18, 'years'));
        if ($('[name="date_hire"]').length > 0)
            $('[name="date_hire"]').parents('.date').data('DateTimePicker').minDate(moment('2016-08-29')).maxDate(moment());
        $('.date').on('dp.change', function (e) {
            if (($(this).children('[name="date_hire"]').length > 0) || ($(this).children('[name="date_birth"]').length > 0)) {
                if (new Date($('[name="date_hire"]').val()) < new Date($('[name="date_birth"]').val())) {
                    swal('Date of Birth cannot be after Date of Joining');
                    $(this).children('input').val('');
                }
            }
        });
    }

    if ($('.datepicker').length > 0) {
        $(".datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',
            maxViewMode: 2,
        });
    }

    if ($('.cancel-button').length > 0) {
        $('.cancel-button').click(function (e) {
            e.preventDefault();
            window.location.href = $(this).find('a').attr('href');
        });
    }

    $('[name="mobile"], [name="mobile2"]').attr('maxlength', '10');
    $('[name="mobile"], [name="mobile2"]').keypress(function (e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if ((charCode >= 48 && charCode <= 57)) {
            return true;
        } else {
            return false;
        }
    });

    //hide overlay
    $('form button[type="submit"], form input[type="submit"]').on('click', function () {
        $('div.overlay').removeClass('hide');
    });

    $('#example1').on('search.dt page.dt length.dt', function () {
        binding();
    });
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

function validateFields(el) {
    var isValid = true;
    el.each(function () {
        if ($(this).val() === '')
            isValid = false;
    });
    return isValid;
}

//functions defined
function binding() {
    
    //For delete button issue
    $('div.overlay').removeClass('hide');
    setTimeout(function () {
        if ($('form.delete').length > 0) {
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

        }
        $('div.overlay').addClass('hide');
    }, 1200);
}