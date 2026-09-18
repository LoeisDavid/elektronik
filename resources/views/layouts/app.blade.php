<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>
</head>

<body>

    @include('components.navbar')

    <div class="flex">

        @include('components.sidebar')

        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

    @include('components.footer')

</body>
</html>