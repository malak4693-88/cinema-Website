<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Login</title>
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="site-body login-body">
    <main class="login-page">
        <section class="login-card">
            <p class="eyebrow">Admin Area</p>
            <h1>Cinema Login</h1>
            <p>Login to manage movies, images, seats, and release dates.</p>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="movie-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}">
                </div>

                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password">
                </div>

                <div class="form-actions">
                    <button class="solid-button" type="submit">Login</button>
                    <a class="outline-button" href="{{ route('home') }}">Home</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
