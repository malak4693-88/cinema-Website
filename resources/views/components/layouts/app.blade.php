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
                {{-- Logout sends the logout request to Laravel and clears the username session. --}}
                <form class="nav-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="soft-button" type="submit">Logout</button>
                </form>
            </nav>
        </header>
    @endif

    {{-- The page content is inserted here when using <x-layouts.app>. --}}
    {{ $slot }}
</body>
</html>
