<!-- Stylesheets -->
<link href="{{ asset('public/assets/css/bootstrap.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/css/main.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/css/responsive.css') }}" rel="stylesheet">

<link rel="shortcut icon" href="logo.ico" type="image/x-icon">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">

@isset($styles)
    @include('web.includes.assets.styles.'.$styles)
@endisset

@isset($assets)
    @include('web.includes.assets.styles.'.$assets)
@endisset