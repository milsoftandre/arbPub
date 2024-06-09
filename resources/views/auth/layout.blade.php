<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('pageTitle')</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />

    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ asset("assets/media/logos/favicon.ico") }}" />

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset("assets/plugins/global/plugins.bundle.css") }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset("assets/css/style.bundle.css") }}" rel="stylesheet" type="text/css" />

</head>
<body id="kt_body" class="bg-body">


@yield('content')



<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="fas fa-arrow-up"></i>
</div>


<script>var hostUrl = "assets/";</script>

<script src="{{ asset("assets/plugins/global/plugins.bundle.js") }}"></script>
<script src="{{ asset("assets/js/scripts.bundle.js") }}"></script>

<script src="{{ asset("assets/js/custom/widgets.js") }}"></script>
<script src="{{ asset("assets/js/custom/apps/chat/chat.js") }}"></script>
<script src="{{ asset("assets/js/custom/modals/create-app.js") }}"></script>
<script src="{{ asset("assets/js/custom/modals/upgrade-plan.js") }}"></script>

</body>

</html>