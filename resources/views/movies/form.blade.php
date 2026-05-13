<x-layouts.app :title="$movie->exists ? 'Edit Movie' : 'Add Movie'" :username="$username" body-class="site-body dashboard-body">
    <main class="form-page">
        <section class="movie-form-card">
            <p class="eyebrow">Cinema Dashboard</p>
            <h1>{{ $movie->exists ? 'Edit Movie' : 'Add Movie' }}</h1>
            <p>Welcome, {{ $username }}</p>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="movie-form" method="POST" action="{{ $movie->exists ? route('movies.update', $movie) : route('movies.store') }}" enctype="multipart/form-data">
                @csrf

                @if ($movie->exists)
                    @method('PUT')
                @endif

                <div>
                    <label for="movie_name">Movie Name</label>
                    <input id="movie_name" type="text" name="movie_name" value="{{ old('movie_name', $movie->movie_name ?? '') }}">
                </div>

                <div>
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        @php($selectedGenre = old('genre', $movie->genre ?? ''))
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
                    <label for="image">Movie Image</label>
                    <input id="image" type="file" name="image" accept="image/*">

                    @if ($movie->exists && $movie->image)
                        <div class="current-image">
                            <img src="{{ str_starts_with($movie->image, 'http') ? $movie->image : asset($movie->image) }}" alt="{{ $movie->movie_name }}">
                            <span>Current image</span>
                        </div>
                    @endif
                </div>

                <div>
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $movie->description ?? '') }}</textarea>
                </div>

                <div class="form-actions">
                    <button class="solid-button" type="submit">{{ $movie->exists ? 'Update Movie' : 'Save Movie' }}</button>
                    <a class="outline-button" href="{{ route('dashboard') }}">Back</a>
                </div>
            </form>
        </section>
    </main>
</x-layouts.app>
