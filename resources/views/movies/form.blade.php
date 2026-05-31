<x-layouts.app :title="$movie->exists ? 'Edit Movie' : 'Add Movie'" :username="$username" body-class="site-body dashboard-body">
    <main class="form-page">
        <section class="movie-form-card">
            {{-- The same form page is used for both adding and editing movies. --}}
            <p class="eyebrow">Cinema Dashboard</p>
            <h1>{{ $movie->exists ? 'Edit Movie' : 'Add Movie' }}</h1>
            <p>Welcome, {{ $username }}</p>

            <section class="tmdb-panel">
                <div>
                    <p class="eyebrow">TMDb API</p>
                    <h2>Search Movie From TMDb</h2>
                    <p>Search by movie name, choose a result, and the form will fill automatically.</p>
                </div>

                <div class="tmdb-search-row">
                    <input id="tmdb-search-input" type="text" placeholder="Example: Inception">
                    <button class="soft-button" id="tmdb-search-button" type="button">Search TMDb</button>
                </div>

                <p id="tmdb-message" class="tmdb-message" hidden></p>
                <div id="tmdb-results" class="tmdb-results"></div>
            </section>

            {{-- Validation errors appear here if the form data is not correct. --}}
            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- If the movie exists, submit to update. Otherwise, submit to store. --}}
            <form class="movie-form" method="POST" action="{{ $movie->exists ? route('movies.update', $movie) : route('movies.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="image" id="image" value="{{ old('image', $movie->image ?? '') }}">

                {{-- PUT is required only when editing an existing movie. --}}
                @if ($movie->exists)
                    @method('PUT')
                @endif

                {{-- old() keeps entered values after validation errors. --}}
                <div>
                    <label for="movie_name">Movie Name</label>
                    <input id="movie_name" type="text" name="movie_name" value="{{ old('movie_name', $movie->movie_name ?? '') }}">
                </div>

                <div>
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        @php($selectedGenre = old('genre', $movie->genre ?? ''))
                        {{-- Genre dropdown helps keep genre values clean and consistent. --}}
                        <option value="">Choose genre</option>
                        @foreach (['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Documentary'] as $genre)
                            <option value="{{ $genre }}" @selected($selectedGenre === $genre)>{{ $genre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="duration">Duration</label>
                    <input id="duration" type="number" name="duration" value="{{ old('duration', $movie->duration ?? '') }}">
                </div>

                <div>
                    <label for="release_date">Release Date</label>
                    <input id="release_date" type="date" name="release_date" value="{{ old('release_date', $movie->exists ? $movie->release_date->format('Y-m-d') : '') }}">
                </div>

                <div>
                    <label for="release_place">Release Place</label>
                    <input id="release_place" type="text" name="release_place" value="{{ old('release_place', $movie->release_place ?? '') }}">
                </div>

                <div>
                    <label for="language">Language</label>
                    <select id="language" name="language">
                        @php($selectedLanguage = old('language', $movie->language ?? ''))
                        {{-- Language dropdown gives common language choices. --}}
                        <option value="">Choose language</option>
                        @foreach (['Arabic', 'English', 'French', 'Spanish', 'Italian', 'German', 'Turkish', 'Hindi', 'Japanese', 'Korean', 'Chinese'] as $language)
                            <option value="{{ $language }}" @selected($selectedLanguage === $language)>{{ $language }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="director">Director</label>
                    <input id="director" type="text" name="director" value="{{ old('director', $movie->director ?? '') }}">
                </div>

                <div>
                    <label for="age_rating">Age Rating</label>
                    <input id="age_rating" type="text" name="age_rating" value="{{ old('age_rating', $movie->age_rating ?? '') }}">
                </div>

                <div>
                    <label for="ticket_price">Ticket Price</label>
                    <input id="ticket_price" type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', $movie->ticket_price ?? '') }}">
                </div>

                <div>
                    <label for="available_seats">Available Seats</label>
                    <input id="available_seats" type="number" name="available_seats" value="{{ old('available_seats', $movie->available_seats ?? '') }}">
                </div>

                <div>
                    <label for="image_file">Movie Image</label>
                    <input id="image_file" type="file" name="image_file" accept="image/*">

                    @php($posterPreview = old('image', $movie->image ?? ''))
                    <div id="tmdb-image-preview" class="current-image" @if (! $posterPreview) hidden @endif>
                        <img id="tmdb-image-preview-img" src="{{ $posterPreview ? (str_starts_with($posterPreview, 'http') ? $posterPreview : asset($posterPreview)) : '' }}" alt="Movie poster preview">
                        <span>{{ $posterPreview && str_starts_with($posterPreview, 'http') ? 'TMDb poster' : 'Current image' }}</span>
                    </div>
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $movie->description ?? '') }}</textarea>
                </div>

                <div class="form-actions">
                    {{-- Button text changes depending on add or edit mode. --}}
                    <button class="solid-button" type="submit">{{ $movie->exists ? 'Update Movie' : 'Save Movie' }}</button>
                    <a class="outline-button" href="{{ route('dashboard') }}">Back</a>
                </div>
            </form>
        </section>
    </main>

    <script>
        const tmdbSearchUrl = @json(route('movies.tmdb.search'));
        const tmdbDetailsUrl = @json(url('/movies/tmdb'));
        const tmdbSearchInput = document.getElementById('tmdb-search-input');
        const tmdbSearchButton = document.getElementById('tmdb-search-button');
        const tmdbResults = document.getElementById('tmdb-results');
        const tmdbMessage = document.getElementById('tmdb-message');

        const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[character]));

        const setTmdbMessage = (message, isError = false) => {
            tmdbMessage.hidden = !message;
            tmdbMessage.textContent = message;
            tmdbMessage.classList.toggle('is-error', isError);
        };

        const setInputValue = (id, value) => {
            const input = document.getElementById(id);

            if (input && value !== null && value !== undefined && value !== '') {
                if (input.tagName === 'SELECT' && !Array.from(input.options).some((option) => option.value === value)) {
                    input.add(new Option(value, value));
                }

                input.value = value;
            }
        };

        const fillMovieForm = (movie) => {
            const posterUrl = movie.poster_url || movie.image;

            setInputValue('movie_name', movie.movie_name);
            setInputValue('genre', movie.genre);
            setInputValue('duration', movie.duration);
            setInputValue('release_date', movie.release_date);
            setInputValue('release_place', movie.release_place);
            setInputValue('language', movie.language);
            setInputValue('director', movie.director);
            setInputValue('age_rating', movie.age_rating);
            setInputValue('image', posterUrl);

            if (posterUrl) {
                document.getElementById('tmdb-image-preview-img').src = posterUrl;
                document.getElementById('tmdb-image-preview').hidden = false;
            }

            if (movie.description) {
                document.getElementById('description').value = movie.description;
            }

            setTmdbMessage('Movie details added to the form. You can edit anything before saving.');
        };

        const loadMovieDetails = async (tmdbId) => {
            setTmdbMessage('Loading movie details...');

            try {
                const response = await fetch(`${tmdbDetailsUrl}/${tmdbId}`, {
                    headers: {
                        Accept: 'application/json'
                    }
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Could not load movie details.');
                }

                fillMovieForm(await response.json());
            } catch (error) {
                setTmdbMessage(error.message, true);
            }
        };

        tmdbSearchButton.addEventListener('click', async () => {
            const query = tmdbSearchInput.value.trim();

            if (query.length < 2) {
                setTmdbMessage('Please type at least 2 letters.', true);
                return;
            }

            setTmdbMessage('Searching TMDb...');
            tmdbResults.innerHTML = '';

            try {
                const response = await fetch(`${tmdbSearchUrl}?query=${encodeURIComponent(query)}`, {
                    headers: {
                        Accept: 'application/json'
                    }
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'TMDb search failed. Check your API key.');
                }

                const movies = await response.json();

                if (!movies.length) {
                    setTmdbMessage('No movies found.', true);
                    return;
                }

                setTmdbMessage('Choose one movie to fill the form.');
                tmdbResults.innerHTML = movies.map((movie) => `
                    <button class="tmdb-result-card" type="button" data-tmdb-id="${movie.id}">
                        ${movie.poster_url ? `<img src="${escapeHtml(movie.poster_url)}" alt="${escapeHtml(movie.title)}">` : '<span>No Poster</span>'}
                        <strong>${escapeHtml(movie.title)}</strong>
                        <small>${escapeHtml(movie.release_date || 'No release date')}</small>
                    </button>
                `).join('');
            } catch (error) {
                setTmdbMessage(error.message, true);
            }
        });

        tmdbResults.addEventListener('click', (event) => {
            const card = event.target.closest('[data-tmdb-id]');

            if (card) {
                loadMovieDetails(card.dataset.tmdbId);
            }
        });
    </script>
</x-layouts.app>
