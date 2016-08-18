<html>
<head>
    <title>Laravel</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
            margin-bottom: 40px;
        }

        .quote {
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">Geo location test</div>
        <div class="quote"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">We'll find what your looking for</div>
                        <div class="panel-body">
                            @if(Session::has('message'))
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        <p>{{ Session::get('message') }}</p>
                                        @foreach(Session::get('data') as $key => $value)
                                        <p> {{ $key }} : {{ $value }} </p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="form-horizontal" role="form" method="POST" action="{{ url('test/geolocation') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Key word</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="keyword" value="{{ old('email') }}">
                                        <input type="hidden" id="latitude" name="latitude">
                                        <input type="hidden" id="longitude" name="longitude">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">Hit me!</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
<script>
$(document).on('ready', function()
{
    console.log('im ready');
    $.getJSON('//freegeoip.net/json/?callback=?', function(data) {
        console.log(JSON.stringify(data, null, 2));
        geodata = data;
        $('#latitude').val(geodata.latitude);
        $('#longitude').val(geodata.longitude);
    });
});
</script>
