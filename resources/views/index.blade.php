<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Website</title>
</head>
<body>
    <header>
        <h1>Cinema Website</h1>

        <nav>
            <a href="{{ route('home') }}">Home</a>

            @if ($username)
                <a href="{{ route('projects.index') }}">Dashboard</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}">Login</a>
            @endif
        </nav>
    </header>

    <main>
        <section>
            <h2>Welcome to Our Cinema</h2>
            <p>Discover movies, show details, ticket prices, available seats, and cinema releases.</p>

            @if ($username)
                <p>You are logged in as {{ $username }}.</p>
                <p>
                    <a href="{{ route('projects.index') }}">Open Dashboard</a>
                </p>
            @else
                <p>
                    <a href="{{ route('login.form') }}">Login to Manage Movies</a>
                </p>
            @endif
        </section>

        <section>
            <h2>Now Showing</h2>

            @forelse ($latestMovies as $movie)
                <article>
                    <h3>{{ $movie->movie_name }}</h3>
                    <p>Genre: {{ $movie->genre }}</p>
                    <p>Duration: {{ $movie->duration }} minutes</p>
                    <p>Release Date: {{ $movie->release_date->format('Y-m-d') }}</p>
                    <p>Language: {{ $movie->language }}</p>
                    <p>Director: {{ $movie->director }}</p>
                    <p>Age Rating: {{ $movie->age_rating }}</p>
                    <p>Ticket Price: {{ $movie->ticket_price }}</p>
                    <p>Available Seats: {{ $movie->available_seats }}</p>

                    @if ($movie->description)
                        <p>{{ $movie->description }}</p>
                    @endif
                </article>
            @empty
                <p>No movies have been added yet.</p>
            @endforelse
        </section>

        <section>
            <h2>About the Cinema</h2>
            <p>Our cinema website helps visitors browse movie information and helps admins manage the movie schedule.</p>
        </section>

        <section>
            <h2>Admin Area</h2>
            <p>The dashboard is protected by session validation. Only logged in users can add, edit, delete, and search movies.</p>

            @if (! $username)
                <p>
                    <a href="{{ route('login.form') }}">Go to Login Page</a>
                </p>
            @endif
        </section>
    </main>
</body>
</html>
