<!DOCTYPE html>
<html lang="en">
    @include('header')
    <body>
        <div id="app">
            @yield('content')
        </div>
    </body>

    <script src="https://js.stripe.com/v3"></script>
{{--    <script src="/js/osm-buildings.js"></script>--}}
    <script src="/js/app.js"></script>
</html>
