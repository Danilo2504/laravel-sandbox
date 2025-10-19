<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ config('app.name', 'Laravel') }}</title>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- <link rel="stylesheet" href="{{asset('assets/vendor/fontawesome-v6/css/all.min.css')}}"> --}}

@vite(['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js', 'resources/js/main.js'])