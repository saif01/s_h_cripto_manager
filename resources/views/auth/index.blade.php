<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('common.demo-icon')
    <title>Cost Deposit</title>
    @vite('resources/sass/app.scss')
</head>

<body>
    <div id="app">
        <div v-if="preloader" class="loader">
            <div class="out_loader">
                <div class="img_spinner_loader"></div>
            </div>
        </div>
        <index-component></index-component>
    </div>

    @vite('resources/js/auth/app.js')
</body>

</html>
