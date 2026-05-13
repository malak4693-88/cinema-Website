<x-layouts.app title="Cinema Dashboard" :username="$username" body-class="site-body dashboard-body">
    <main id="dashboard-content" class="studio-dashboard {{ isset($editingMovie) || $errors->any() ? 'hidden-form' : '' }}">
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
    </main>

    <main id="movie-form-page" class="form-page {{ isset($editingMovie) || $errors->any() ? '' : 'hidden-form' }}">
        <section id="movie-form" class="movie-form-card">
            <p class="eyebrow">Movie Form</p>
            <h2>{{ isset($editingMovie) ? 'Edit Movie' : 'Add Movie' }}</h2>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="movie-form" method="POST" action="{{ isset($editingMovie) ? route('movies.update', $editingMovie) : route('movies.store') }}" enctype="multipart/form-data">
                @csrf

                @if (isset($editingMovie))
                    @method('PUT')
                @endif

                @include('movies.form', ['movie' => $editingMovie ?? null])

                <div class="form-actions">
                    <button class="solid-button" type="submit">{{ isset($editingMovie) ? 'Update' : 'Save' }}</button>

                    @if (isset($editingMovie))
                        <a class="outline-button" href="{{ route('dashboard') }}">Cancel Edit</a>
                    @else
                        <button class="outline-button" type="button" onclick="document.getElementById('movie-form-page').classList.add('hidden-form'); document.getElementById('dashboard-content').classList.remove('hidden-form'); document.getElementById('movie-list-page').classList.remove('hidden-form');">Back</button>
                    @endif
                </div>
            </form>
        </section>
    </main>

    <main id="movie-list-page" class="studio-dashboard {{ isset($editingMovie) || $errors->any() ? 'hidden-form' : '' }}">
        <section id="movie-list" class="studio-library">
            <div class="studio-library-head">
                <div>
                    <p class="eyebrow">Movie List</p>
                    <h2>All Movies</h2>
                </div>

                <div class="library-actions">
                    <button class="add-movie-button" type="button" onclick="document.getElementById('dashboard-content').classList.add('hidden-form'); document.getElementById('movie-list-page').classList.add('hidden-form'); document.getElementById('movie-form-page').classList.remove('hidden-form'); window.scrollTo({ top: 0, behavior: 'smooth' });">Add New Movie</button>

                    <form class="search-form" method="GET" action="{{ route('dashboard') }}">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search movies">
                        <button class="soft-button" type="submit">Search</button>
                        <a class="clear-link" href="{{ route('dashboard') }}">Clear</a>
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
                                        <button class="solid-button" type="button" onclick="document.getElementById('dashboard-content').classList.add('hidden-form'); document.getElementById('movie-list-page').classList.add('hidden-form'); document.getElementById('movie-form-page').classList.remove('hidden-form'); window.scrollTo({ top: 0, behavior: 'smooth' });">Add Movie</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</x-layouts.app>
