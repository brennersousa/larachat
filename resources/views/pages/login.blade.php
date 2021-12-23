<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset(mix('assets/css/bootstrap.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('assets/css/login.css')) }}">
    <title>
        LaraChat
        @hasSection('title')
            - @yield('title')
        @endif
    </title>
</head>

<body>

    <form class="form" autocomplete="off" action="{{ route('login') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="email" class="col-form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="col-form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="col text-end">
            <button type="submit" class="btn btn-primary mt-3 text-white">Login</button>
        </div>
    </form>

    @include('includes.ajax-response')

    <script src="{{ asset(mix('assets/js/vendor-login.js')) }}"></script>
    <script src="{{ asset(mix('assets/js/login.js')) }}"></script>
</body>

</html>
