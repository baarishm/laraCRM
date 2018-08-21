$('document').ready(function () {
    if ($('.date').length > 0) {
        $('.date').data('DateTimePicker').date(new Date()).format('DD MMM YYYY');
    }
});

