<!doctype html>
<html lang="en" data-theme="light"> {{--Daisy Ui Theme Configuration--}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{asset('storage/nexus_logo_v2.png')}}" type="image/x-icon">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
