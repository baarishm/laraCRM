$('document').ready(function () {
    if ($('.date').length > 0) {
        $('.date').data('DateTimePicker').date(new Date()).format('DD MMM YYYY');
    }
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