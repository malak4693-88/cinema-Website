<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Dashboard</title>
    <style>
        .dashboard-locked body {
            visibility: hidden;
        }
    </style>
    <script>
        document.documentElement.classList.add('dashboard-locked');
    </script>
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="site-body dashboard-body">
    <header class="main-header dashboard-header">
        <a class="brand" href="{{ route('home') }}">Cinema Website</a>

        <nav class="main-nav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('dashboard', ['access' => $dashboardAccess]) }}">Dashboard</a>
            <form class="nav-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="soft-button" type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <main class="studio-dashboard">
        <section class="studio-hero">
            <div>
                <p class="eyebrow">Cinema Control</p>
                <h1>Movie Dashboard</h1>
                <p>Manage posters, release dates, ticket prices, seats, and movie details.</p>
            </div>

            <div class="dashboard-user-card">
                <span>Logged in as</span>
                <strong>{{ $username }}</strong>
            </div>
        </section>

        <section class="studio-stats">
            <article>
                <div>
                    <strong>{{ $totalMovies }}</strong>
                    <span>Total Movies</span>
                </div>
                <p>All movies in your cinema database.</p>
            </article>
            <article>
                <div>
                    <strong>{{ $releasedMovies }}</strong>
                    <span>Released</span>
                </div>
                <p>Movies that are available now.</p>
            </article>
            <article>
                <div>
                    <strong>{{ $unreleasedMovies }}</strong>
                    <span>Unreleased</span>
                </div>
                <p>Movies coming later.</p>
            </article>
        </section>

        @if (session('success'))
            <p class="success-message dashboard-success">{{ session('success') }}</p>
        @endif

        <section class="studio-library">
            <div class="studio-library-head">
                <div>
                    <p class="eyebrow">Movie List</p>
                    <h2>All Movies</h2>
                </div>

                <div class="library-actions">
                    <a class="add-movie-button" href="{{ route('movies.create') }}">Add New Movie</a>

                    <form class="search-form" method="GET" action="{{ route('dashboard') }}">
                        <input type="hidden" name="access" value="{{ $dashboardAccess }}">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search movies">
                        <button class="soft-button" type="submit">Search</button>
                        <a class="clear-link" href="{{ route('dashboard', ['access' => $dashboardAccess]) }}">Clear</a>
                    </form>
                </div>
            </div>

            <div class="studio-table-wrap">
                <table class="studio-table">
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Movie</th>
                            <th>Genre</th>
                            <th>Release</th>
                            <th>Language</th>
                            <th>Price</th>
                            <th>Seats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movies as $movie)
                            <tr>
                                <td>
                                    <div class="studio-poster">
                            @if ($movie->image)
                                <img src="{{ str_starts_with($movie->image, 'http') ? $movie->image : asset($movie->image) }}" alt="{{ $movie->movie_name }}">
                            @else
                                <span>No Image</span>
                            @endif
                        </div>
                                </td>
                                <td>
                                    <strong>{{ $movie->movie_name }}</strong>
                                    <p>{{ $movie->director }} · {{ $movie->duration }} min · {{ $movie->age_rating }}</p>
                                </td>
                                <td>{{ $movie->genre }}</td>
                                <td>{{ $movie->release_date->format('Y-m-d') }}</td>
                                <td>{{ $movie->language }}</td>
                                <td>{{ $movie->ticket_price }}</td>
                                <td>{{ $movie->available_seats }}</td>
                                <td>
                                    @if ($movie->release_date->isFuture())
                                        <span class="status-pill upcoming">Upcoming</span>
                                    @else
                                        <span class="status-pill released">Released</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons compact-actions">
                                        <a class="soft-button" href="{{ route('movies.edit', $movie) }}">Edit</a>
                                        <form method="POST" action="{{ route('movies.destroy', $movie) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit" onclick="return confirm('Delete this movie?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <h3>No movies found</h3>
                                        <p>Add your first movie to start building the cinema dashboard.</p>
                                        <a class="solid-button" href="{{ route('movies.create') }}">Add Movie</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const dashboardTabKey = @json(session('dashboard_tab_key'));
        const dashboardUrl = @json(route('dashboard'));
        const loginRequiredUrl = @json(route('dashboard.login.required'));

        if (window.location.search.includes('access=')) {
            sessionStorage.setItem('dashboard_tab', dashboardTabKey);
            window.history.replaceState({}, '', dashboardUrl);
        }

        if (!window.location.search.includes('access=') && sessionStorage.getItem('dashboard_tab') !== dashboardTabKey) {
            window.location.replace(loginRequiredUrl);
        } else {
            document.documentElement.classList.remove('dashboard-locked');
        }
    </script>
</body>
</html>
