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
                <a href="{{url('test/geolocation')}}"><button class="btn btn-success">another query?</button></a>
                <div class="col-md-8 col-md-offset-2">
                            @foreach($data as $item)
                            <div class="panel panel-default">
                            <div class="panel-heading">{{ $item->name }}</div>
                                <div class="panel-body">


                                <p> location :
                                    {{ (isset($item->location->address)) ? $item->location->address : 'location not defined.' }}
                                </p>
                                <p> Contact Number :
                                    {{ (isset($item->contact->phone)) ? $item->contact->phone : 'no contact details.' }}
                                </p>


                                <p> here now : {{ $item->hereNow->count }}</p>
                            </div>
                        </div>
                    @endforeach
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
        });
    });
</script>
