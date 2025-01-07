<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS_SYSTEM</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    @vite('resources/css/app.css')
    @livewireStyles()
    @cloudinaryJS
</head>
<body>
    @yield('content')

    @livewireScripts()
</body>
</html>