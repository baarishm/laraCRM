<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,IE=11,IE=9,IE=8">
    <title>PlusMinus</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/favicon.ico">
    <link href="~/Content/font-awesome.min.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css?family=Lato:300i,400,400i,700,700i,900|Open+Sans:400,400i,600,600i,700,700i,800" rel="stylesheet">

    <link href="~/Content/daterangepicker.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="~/Content/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #6F85AD;
            color: #fff;
        }

        .session-exp {
            padding-top: 100px;
            text-align: center;
        }

        .session-exp h1 {
            font-size: 50px;
            text-transform: uppercase;
            margin: 0px;
            margin-bottom: 20px;
        }

        .session-exp h3 {
            font-size: 18px;
            margin: 0;
            line-height: 24px;
        }

        .session-exp p {
            font-size: 70px;
            margin: 0px;
            margin-top: 20px;
        }

        .session-exp a {
            display: inline-block;
            background: #fff;
            width: 160px;
            padding: 13px;
            border: none;
            box-shadow: 1px 1px 4px;
            border-radius: 3px;
            margin: 30px 0px;
            font-weight: bold;
            color: #1e9bd7;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <section class="session-exp">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p><i class="fa fa-clock-o" aria-hidden="true"></i></p>
                    <h1>Session Expired</h1>
                    <h3>Your Session has expired.</h3>
                    <h3>Please click below link to redirect to the homepage.</h3>
                   
                    <a class="btn-default" href="{{ url('/') }}">Back to Home</a>
                </div>
            </div>
        </div>
    </section>

</body>
</html>