<!doctype html>
<html lang="pt-br">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset(mix('assets/css/bootstrap.css')) }}">
    {{-- <link rel="stylesheet" href="{{ asset(mix('assets/admin/css/vendor.css')) }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/js/jquery-ui/jquery-ui.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/css/style.css')) }}">
    @hasSection('css')
        @yield('css')
    @endif
    
    <title>
        LaraChat
        @hasSection('title')
            - @yield('title')
        @endif
    </title>
</head>

<body>

    <div class="container-fluid">
        {{-- <div class="row"> --}}
            <main class="main-content col">
                @yield("content")
            </main>

            <!-- SYSTEM MODAL -->
            {{-- @include('admin.includes.system-modal') --}}
            <!-- END SYSTEM MODAL -->

            <!-- MODAL TO CONFIRM ACTION -->
            {{-- @include('admin.includes.modal-confirm-action') --}}
            <!-- END MODAL TO CONFIRM ACTION -->

            <!-- AJAX_LOAD AND RESPONSE -->
            {{-- @include('admin.includes.ajax-response') --}}
            <!-- END AJAX_LOAD AND RESPONSE -->
        {{-- </div> --}}
    </div>

    <script src="{{ asset(mix('assets/js/vendor.js')) }}"></script>
    <script src="{{ asset(mix('assets/js/scripts.js')) }}"></script>
    @hasSection('scripts')
        @yield('scripts')
    @endif
</body>

</html>
