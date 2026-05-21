{{-- This layout receives a title, optional username, and body class from each page. --}}
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

    {{-- This lock hides protected pages until the sessionStorage tab check passes. --}}
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

    {{-- Optional extra head content can be inserted by a page if needed. --}}
    {{ $head ?? '' }}
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="{{ $bodyClass ?? 'site-body' }}">
    {{-- The dashboard navbar appears only when a username exists. --}}
    @if ($username)
        <header class="main-header dashboard-header">
            <a class="brand" href="{{ route('home') }}">Cinema Website</a>

            <nav class="main-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                {{-- Logout clears browser tab flags and sends the logout request to Laravel. --}}
                <form class="nav-form" method="POST" action="{{ route('logout') }}" onsubmit="sessionStorage.removeItem('dashboard_tab_allowed'); sessionStorage.removeItem('tab_logged_out');">
                    @csrf
                    <button class="soft-button" type="submit">Logout</button>
                </form>
            </nav>
        </header>
    @endif

    {{-- The page content is inserted here when using <x-layouts.app>. --}}
    {{ $slot }}

    {{-- This script keeps dashboard access limited to the same browser tab that logged in. --}}
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
