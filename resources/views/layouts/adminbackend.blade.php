<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS_SYSTEM</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/pos.png') }}" type="image/png">
    
    @vite('resources/css/app.css')
    @livewireStyles()
</head>
<body class="bg-gray-800 text-white">
    <div class="flex h-screen">
        @livewire('sidebar')
        <div class="content">
            @yield('content')
        </div>
    </div>

    @livewireScripts()
    @stack('scripts')
</body>
</html>