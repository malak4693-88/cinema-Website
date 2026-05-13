@props([
    'title' => 'Cinema Website',
    'username' => null,
    'bodyClass' => 'site-body',
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cinema Website' }}</title>
    @if ($username)
        <style>
            .dashboard-locked body {
                visibility: hidden;
            }
        </style>
        <script>
            document.documentElement.classList.add('dashboard-locked');
        </script>
    @endif
    {{ $head ?? '' }}
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="{{ $bodyClass ?? 'site-body' }}">
    @if ($username)
        <header class="main-header dashboard-header">
            <a class="brand" href="{{ route('home') }}">Cinema Website</a>

            <nav class="main-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <form class="nav-form" method="POST" action="{{ route('logout') }}" onsubmit="sessionStorage.removeItem('dashboard_tab_allowed'); sessionStorage.removeItem('tab_logged_out');">
                    @csrf
                    <button class="soft-button" type="submit">Logout</button>
                </form>
            </nav>
        </header>
    @endif

    {{ $slot }}

    @if ($username)
        <script>
            if (sessionStorage.getItem('dashboard_tab_allowed') !== 'yes') {
                sessionStorage.removeItem('dashboard_tab_allowed');
                sessionStorage.setItem('tab_logged_out', 'yes');
                window.location.replace("{{ route('login.form') }}");
            } else {
                document.documentElement.classList.remove('dashboard-locked');
            }
        </script>
    @endif
</body>
</html>
