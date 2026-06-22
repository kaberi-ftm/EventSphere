<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EventSphere')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body{
            background:#f4f7fc;
            overflow-x:hidden;
        }

        .sidebar{
            width:260px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;
            background:#0f172a;
            color:white;
            transition:.3s;
            z-index:1000;
        }

        .sidebar-header{
            padding:25px;
            font-size:24px;
            font-weight:bold;
            border-bottom:1px solid rgba(255,255,255,.1);
        }

        .sidebar a{
            color:#cbd5e1;
            text-decoration:none;
            display:block;
            padding:14px 25px;
            transition:.3s;
        }

        .sidebar a:hover{
            background:#2563eb;
            color:white;
        }

        .content{
            margin-left:260px;
            min-height:100vh;
        }

        .top-navbar{
            height:70px;
            background:white;
            box-shadow:0 3px 15px rgba(0,0,0,.05);
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:0 30px;
        }

        .page-content{
            padding:30px;
        }

        .card{
            border:none;
            border-radius:18px;
            box-shadow:0 8px 25px rgba(0,0,0,.08);
        }

        .footer{
            padding:20px;
            text-align:center;
            color:#777;
        }
    </style>

</head>

<body>

<div class="sidebar">

    @include('partials.sidebar')

</div>

<div class="content">

    @include('partials.navbar')

    <div class="page-content">

        @yield('content')

    </div>

    @include('partials.footer')

</div>

</body>

</html>