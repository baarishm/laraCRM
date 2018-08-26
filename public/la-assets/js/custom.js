$('document').ready(function () {
    if ($('.date').length > 0) {
		$('.date').each(function(){
			var date = new Date();
			var child_input = $(this).find('input');
			if(child_input.val() != ''){
				date = new Date(child_input.val());
			}
			$(this).data('DateTimePicker').date(date).format('DD MMM YYYY');
			if(child_input.attr('name') == 'start_date' || child_input.attr('name') == 'end_date'){
				$(this).on('dp.change', function(e){ 
					if(new Date($('form input[name="start_date"]').val()) > new Date($('form input[name="end_date"]').val())){
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