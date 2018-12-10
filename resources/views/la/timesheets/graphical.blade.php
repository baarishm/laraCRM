@extends("la.layouts.app")
@section("contentheader_title")
<a href="{{ url(config('laraadmin.adminRoute') . '/projects') }}">Projects Track</a>
@endsection

@section("section", "Projects")
@section("section_url", url(config('laraadmin.adminRoute') . '/projects'))


@section("main-content")

<button class="btn btn-success m10" onclick="sendMail()">Send As E-Mail</button>

<div id="pop_div_m"></div>
<br>
<br>
<div id="pop_div_w"></div>
<span id="month" style="display: none;"></span>
<span id="week" style="display: none;"></span>

{!! \Lava::render('ColumnChart', 'Work_Monthly', 'pop_div_m'); !!}
{!! \Lava::render('DonutChart', 'Work_Weekly', 'pop_div_w'); !!}

<script>
      function getImageCallbackMonth(event, chart) {
            // This will return in the form of "data:image/png;base64,iVBORw0KGgoAAAAUA..."
            document.getElementById('month').value = chart.getImageURI();
      }

      function getImageCallbackWeek(event, chart) {
            // This will return in the form of "data:image/png;base64,iVBORw0KGgoAAAAUA..."
            document.getElementById('week').value = chart.getImageURI();
      }
      
      function sendMail(){
            var monthly = document.getElementById('month').value;
            var weekly = document.getElementById('week').value;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                  if (this.readyState == 4 && this.status == 200) {

                  }
            };
            xhttp.open("POST", "{{ url(config('laraadmin.adminRoute') . '/project/sendMailWithGraphs') }}", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("monthly=" + monthly + "&weekly=" + weekly + "&_token={{ csrf_token() }}");
      }
</script>
@endsection
