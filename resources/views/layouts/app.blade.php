<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" type="text/css" media="screen" href="{{asset('assets/css/perfect-scrollbar.min.css')}}" />
        <link rel="stylesheet" type="text/css" media="screen" href="{{asset('assets/css/style.css')}}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
        <link defer rel="stylesheet" type="text/css" media="screen" href="{{asset('assets/css/animate.css')}}" />
        <script src="{{asset('assets/js/perfect-scrollbar.min.js')}}"></script>
        <script defer src="{{asset('assets/js/popper.min.js')}}"></script>
        <script defer src="{{asset('assets/js/tippy-bundle.umd.min.js')}}"></script>
        <script defer src="{{asset('assets/js/sweetalert.min.js')}}"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="{{asset('assets/js/alpine-collaspe.min.js')}}"></script>
        <script src="{{asset('assets/js/alpine-persist.min.js')}}"></script>
        <script defer src="{{asset('assets/js/alpine-ui.min.js')}}"></script>
        <script defer src="{{asset('assets/js/alpine-focus.min.js')}}"></script>
        <script defer src="{{asset('assets/js/alpine.min.js')}}"></script>
        <script src="{{asset('assets/js/custom.js')}}"></script>
        @stack('scripts')
    </body>
</html>
