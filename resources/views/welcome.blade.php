<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoManager</title>
    @include('common.demo-icon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <div v-if="preloader" class="loader">
            <div class="out_loader">
                <div class="img_spinner_loader"></div>
            </div>
        </div>
        <index-component authuser="{{ Auth::user() }}"></index-component>
    </div>

    {{-- <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p> --}}

</body>

</html>
