$('document').ready(function () {
    //hide overlay
    $('div.overlay').addClass('hide');

    //date search
    $('#date_search, .date_search').datetimepicker({
        format: 'DD MMM YYYY',
        minDate: moment('2016-08-29')
    });

    //stop keyboard entry
    $('#date_search, .date_search').on('paste keydown', function (e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode != 8) {
            e.preventDefault();
            return false;
        }
    });

    //date inputs
    if ($('.date').length > 0) {
        $('.date').each(function () {
            //show view till year
            $(this).on('dp.show dp.update', function () {
                $(".datepicker-years .picker-switch").removeAttr('title')
                        .css('cursor', 'default')
                        .css('background', 'inherit')
                        .on('click', function (e) {
                            e.stopPropagation();
                        });
            });

            //format and position
            $(this).data('DateTimePicker').format('DD MMM YYYY').widgetPositioning({vertical: 'auto'});

            //fill in old date or today's date
            var child_input = $(this).find('input');
            var date = new Date();
            if (child_input.attr('name') == 'end_date') {
                date = '';
            }
            if (child_input.val() != '') {
                date = new Date(child_input.val());
            }
            else if(child_input.attr('value') != ''){
                date = new Date(child_input.attr('value'));
            }
            $(this).data('DateTimePicker').date(date).useStrict(true).keepInvalid(true);

            //stop keyboard entry
            $(this).on('paste keydown', function (e) {
                if ($(this).find('input').hasClass('date_search') || $(this).hasClass('date_search')) {
                    var charCode = (e.which) ? e.which : e.keyCode;
                    if (charCode != 8) {
                        e.preventDefault();
                        return false;
                    }
                } else {
                    e.preventDefault();
                    return false;
                }
            });

            //start date and end date validation
            if (child_input.attr('name') == 'start_date' || child_input.attr('name') == 'end_date') {
                $(this).on('dp.change', function (e) {
                    var start_date = $('input[name="start_date"]').val();
                    $('[name="end_date"]').parents('.date').data('DateTimePicker').minDate(moment(new Date(start_date)));
                    if (new Date($('input[name="start_date"]').val()) > new Date($('input[name="end_date"]').val())) {
                        $('input[name="end_date"]').parents('.date').data('DateTimePicker').date(start_date);
                    }
                });
            }
        });

        $('.date>input').prop('autocomplete', 'off');

        //dob n doj validation
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

    //datepicker initialization
    if ($('.datepicker').length > 0) {
        $(".datepicker").datepicker({
            autoclose: true,
            format: 'd M yyyy',
            maxViewMode: 2,
        });
    }

    //cancel button redirection updated
    if ($('.cancel-button').length > 0) {
        $('.cancel-button').click(function (e) {
            e.preventDefault();
            window.location.href = $(this).find('a').attr('href');
        });
    }

    //mobile number validation
    $('[name="mobile"], [name="mobile2"]').attr('maxlength', '10');
    $('[name="mobile"], [name="mobile2"]').keypress(function (e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if ((charCode >= 48 && charCode <= 57)) {
            return true;
        } else {
            return false;
        }
    });

    //show overlay
    $('form button[type="submit"], form input[type="submit"]').on('click', function () {
        $('div.overlay').removeClass('hide');
    });

    //hide overlay
    $('button[type="submit"], input[type="submit"]').on('click', function () {
        if (!$(this).closest('form')[0].checkValidity() || $('.error').length != 0) {
            $('div.overlay').addClass('hide');
        }
    });

    $('#example1').on('draw.dt search.dt page.dt length.dt', function (e) {
        binding();
    });

    $('.modal-footer [data-dismiss]').on('click', function () {
        $('.modal-body div.form-group>input').val('');
    });

    //tooltip
    $('.tooltips').tooltip({'placement': 'bottom'});
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

function dateFormatDB(date) {
    var date = new Date(date);
    return date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
}


function validateFields(el) {
    var isValid = true;
    el.each(function () {
        if ($(this).val() === '' || $(this).val() === null)
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
                if (!form.hasClass('binded')) {
                    form.find('button[type="submit"]').on("click", function (e) {
                        e.preventDefault();
                        $('div.overlay').addClass('hide');
                        swal({
                            title: "Are you sure?",
                            text: "You will not be able to recover this action!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Delete",
                            cancelButtonText: "Cancel",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        }).then(function (isConfirm) {
                            if (isConfirm.value) {
                                $('div.overlay').removeClass('hide');
                                form.submit();
                            } else {
                                return false;
                            }
                        });
                    });

                    form.addClass('binded');
                }
            });

        }
        $('div.overlay').addClass('hide');
    }, 1200);
}

function filterDatatableData(d){
	for (var i = 0, len = d.columns.length; i < len; i++) {
		if (!d.columns[i].search.value) delete d.columns[i].search;
		if (d.columns[i].searchable === true) delete d.columns[i].searchable;
		if (d.columns[i].orderable === true) delete d.columns[i].orderable;
		if (d.columns[i].d === d.columns[i].name) delete d.columns[i].name;
	  }
	 delete d.search.regex;
	 return d;
}