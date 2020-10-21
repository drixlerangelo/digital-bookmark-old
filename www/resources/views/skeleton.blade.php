<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page.title')</title>
    @stack('page.style')
</head>
<body>
    <div id="vue-element">
    @yield('page.content')
    </div>
    @stack('page.script')
</body>
</html>
