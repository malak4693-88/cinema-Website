<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Website</title>
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="site-body">
    <header class="main-header">
        <a class="brand" href="{{ route('home') }}">Cinema Website</a>

        <nav class="main-nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="#movies">Movies</a>
            <a href="#about">About</a>

            @if ($username)
                <a href="{{ route('dashboard', ['access' => $dashboardAccess]) }}">Dashboard</a>

                <form class="nav-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="soft-button" type="submit">Logout</button>
                </form>
            @else
                <a class="solid-button" href="{{ route('login.form') }}">Login</a>
            @endif
        </nav>
    </header>

    <main>
        <section class="hero-section">
            <div class="hero-copy">
                <p class="eyebrow">Cinema nights, softly curated</p>
                <h1>Find Your Dream Movie Night</h1>
                <p>Browse releases, ticket prices, directors, languages, and seats in one calm cinema space.</p>

                <div class="hero-actions">
                    @if ($username)
                        <a class="solid-button" href="{{ route('dashboard', ['access' => $dashboardAccess]) }}">Open Dashboard</a>
                    @else
                        <button class="solid-button" type="button">Book a Ticket</button>
                    @endif

                    <a class="outline-button" href="#movies">Explore Movies</a>
                </div>
            </div>

            <div class="hero-note">
                <p>Featured releases, new seats, and quiet dashboard control for your cinema collection.</p>
            </div>
        </section>

        <section id="movies" class="slider-section coverflow-section">
            <p class="eyebrow centered">Now Showing</p>
            <h2>Movie Gallery</h2>
            <p class="section-intro">A soft visual slider for the latest movies in your cinema database.</p>

            <div class="movie-chips">
                <span>Drama</span>
                <span>Action</span>
                <span>Romance</span>
                <span>Animation</span>
                <span>Arabic</span>
                <span>English</span>
            </div>

            <div class="slider-shell coverflow-shell" data-slider>
                <button class="slider-button previous-button" type="button" data-slider-prev aria-label="Previous movie">&#8249;</button>

                <div class="slider-track coverflow-track" data-slider-track>
                    @forelse ($latestMovies as $movie)
                        <article class="slide-card">
                            <div class="slide-image">
                                @if ($movie->image)
                                    <img src="{{ str_starts_with($movie->image, 'http') ? $movie->image : asset($movie->image) }}" alt="{{ $movie->movie_name }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80" alt="{{ $movie->movie_name }}">
                                @endif
                            </div>
                            <div class="slide-info">
                                <span>{{ $movie->genre }}</span>
                                <h3>{{ $movie->movie_name }}</h3>
                                <p>{{ $movie->director }} · {{ $movie->language }} · {{ $movie->duration }} min</p>
                            </div>
                        </article>
                    @empty
                        <article class="slide-card">
                            <div class="slide-image">
                                <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80" alt="Cinema hall">
                            </div>
                            <div class="slide-info">
                                <span>Coming Soon</span>
                                <h3>Add Your First Movie</h3>
                                <p>Login to add posters, dates, seats, and ticket prices.</p>
                            </div>
                        </article>
                        <article class="slide-card">
                            <div class="slide-image">
                                <img src="https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&w=900&q=80" alt="Movie camera">
                            </div>
                            <div class="slide-info">
                                <span>Dashboard Ready</span>
                                <h3>Manage Cinema Releases</h3>
                                <p>Create movie records and they will appear here automatically.</p>
                            </div>
                        </article>
                    @endforelse
                </div>

                <button class="slider-button next-button" type="button" data-slider-next aria-label="Next movie">&#8250;</button>
            </div>

            <div class="slider-dots" data-slider-dots></div>
        </section>

        <section id="about" class="about-section">
            <div class="about-visual">
                <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=900&q=80" alt="Cinema seats">
            </div>

            <div class="about-copy">
                <p class="eyebrow">About Us</p>
                <h2>Know our values and what we provide</h2>
                <p>Our cinema website helps visitors browse movie information and helps admins manage the movie schedule.</p>

                <ul class="feature-list">
                    <li>Movie posters and release details</li>
                    <li>Ticket prices and available seats</li>
                    <li>Protected dashboard for cinema admins</li>
                </ul>
            </div>
        </section>

        <section class="admin-band">
            <h2>Admin Area</h2>
            <p>The dashboard is protected by session validation. Only logged in users can add, edit, delete, search movies, and upload movie images.</p>

            @if (! $username)
                <a class="solid-button" href="{{ route('login.form') }}">Go to Login Page</a>
            @endif
        </section>
    </main>

    <script src="{{ asset('js/slider.js') }}"></script>
</body>
</html>
