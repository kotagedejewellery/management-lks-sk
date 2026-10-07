<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('brand/favicon-32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('brand/apple-touch-icon.png') }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
