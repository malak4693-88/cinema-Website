<x-layouts.app title="Cinema Login" body-class="site-body login-body">
    <main class="login-page">
        <section class="login-card">
            {{-- Login page heading and short explanation for the admin. --}}
            <p class="eyebrow">Admin Area</p>
            <h1>Cinema Login</h1>
            <p>Login to manage movies, images, seats, and release dates.</p>

            {{-- Show validation errors, for example wrong username or password. --}}
            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- The form sends username/password to AuthController@login. --}}
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
</x-layouts.app>
