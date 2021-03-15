<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.auth.head')
<body class="theme-blush">
@yield('page-login')
@include('layouts.auth.scripts')
</body>
</html>
