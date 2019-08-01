<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bencana Lereng Merapi</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.css"/>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/datatables.min.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Bencana Lereng Merapi</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/') }}">Home
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                <?php if(!empty(session()->get('sessionAuth'))){ ?>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('bencana') }}">Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('wilayah') }}">Area</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('bencana/config') }}">Config</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('auth/logout') }}">Logout</a>
                    </li>
                <?php }else{ ?>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('auth') }}">Login</a>
                    </li>
                <?php } ?>
            </ul>
            </div>
        </div>
    </nav>
    <section class="py-5">
        @yield('content')
    </section>
</body>
</html>