<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function index(Request $request): View
    {
        // Read the search value from the URL, for example /dashboard?search=action.
        $search = $request->query('search');

        // Get movies from the database, and filter them only when the user searches.
        $movies = Movie::query()
            ->when($search, function ($query, string $search): void {
                // Search in important movie fields.
                $query->where('movie_name', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('language', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        // Dashboard statistic: count all movies.
        $totalMovies = Movie::count();
        // Dashboard statistic: count movies released today or before today.
        $releasedMovies = Movie::whereDate('release_date', '<=', now()->toDateString())->count();
        // Dashboard statistic: count movies with a future release date.
        $unreleasedMovies = Movie::whereDate('release_date', '>', now()->toDateString())->count();

        // Send movies, search value, username, and statistics to the dashboard view.
        return view('movies.index', [
            'movies' => $movies,
            'search' => $search,
            'username' => session('username'),
            'totalMovies' => $totalMovies,
            'releasedMovies' => $releasedMovies,
            'unreleasedMovies' => $unreleasedMovies,
        ]);
    }

    public function create(): View
    {
        // Open the shared movie form with a new empty Movie object for adding.
        return view('movies.form', [
            'movie' => new Movie(),
            'username' => session('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate the form data before saving.
        $data = $this->validatedMovieData($request);
        unset($data['image_file']);
        // Upload the image if one was selected, then save its path in the data array.
        $data['image'] = $this->storeMovieImage($request) ?? $data['image'] ?? null;

        // Create the new movie record in the movies table.
        Movie::create($data);

        // Return to dashboard with a success message.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie added successfully.');
    }

    public function edit(Movie $movie): View
    {
        // Open the shared movie form with the selected movie data for editing.
        return view('movies.form', [
            'movie' => $movie,
            'username' => session('username'),
        ]);
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        // Validate the edited movie data.
        $data = $this->validatedMovieData($request);
        unset($data['image_file']);

        // Only replace the image when the user uploads a new one.
        if ($request->hasFile('image_file')) {
            // Delete the old image before saving the new image.
            $this->deleteMovieImage($movie->image);
            $data['image'] = $this->storeMovieImage($request);
        } elseif (($data['image'] ?? null) && $data['image'] !== $movie->image) {
            $this->deleteMovieImage($movie->image);
        }

        // Update the selected movie record.
        $movie->update($data);

        // Return to dashboard with a success message.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        // Delete the movie image file before deleting the database record.
        $this->deleteMovieImage($movie->image);
        // Delete the selected movie from the movies table.
        $movie->delete();

        // Return to dashboard with a success message.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie deleted successfully.');
    }

    public function tmdbSearch(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        if (! $this->hasTmdbCredentials()) {
            return response()->json([
                'message' => 'TMDb API key is missing. Add TMDB_API_KEY or TMDB_ACCESS_TOKEN to your .env file.',
            ], 500);
        }

        $response = $this->tmdbRequest('search/movie', [
            'query' => $data['query'],
            'include_adult' => false,
            'language' => 'en-US',
            'page' => 1,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Could not connect to TMDb. Check your API key.',
            ], 502);
        }

        $movies = collect($response->json('results', []))
            ->take(6)
            ->map(fn (array $movie): array => [
                'id' => $movie['id'] ?? null,
                'title' => $movie['title'] ?? $movie['original_title'] ?? 'Untitled',
                'release_date' => $movie['release_date'] ?? null,
                'poster_path' => $movie['poster_path'] ?? null,
                'poster_url' => $this->tmdbPosterUrl($movie['poster_path'] ?? null),
            ])
            ->filter(fn (array $movie): bool => filled($movie['id']))
            ->values()
            ->all();

        return response()->json($movies);
    }

    public function tmdbDetails(string $tmdbId): JsonResponse
    {
        if (! $this->hasTmdbCredentials()) {
            return response()->json([
                'message' => 'TMDb API key is missing. Add TMDB_API_KEY or TMDB_ACCESS_TOKEN to your .env file.',
            ], 500);
        }

        $response = $this->tmdbRequest("movie/{$tmdbId}", [
            'append_to_response' => 'credits',
            'language' => 'en-US',
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Could not load movie details from TMDb.',
            ], 502);
        }

        $movie = $response->json();
        $director = collect($movie['credits']['crew'] ?? [])
            ->firstWhere('job', 'Director');
        $posterUrl = $this->tmdbPosterUrl($movie['poster_path'] ?? null);

        return response()->json([
            'movie_name' => $movie['title'] ?? $movie['original_title'] ?? '',
            'genre' => $movie['genres'][0]['name'] ?? '',
            'duration' => $movie['runtime'] ?? '',
            'release_date' => $movie['release_date'] ?? '',
            'release_place' => $movie['origin_country'][0] ?? '',
            'language' => $this->languageName($movie['original_language'] ?? ''),
            'director' => $director['name'] ?? '',
            'age_rating' => 'PG-13',
            'ticket_price' => '',
            'available_seats' => '',
            'poster_path' => $movie['poster_path'] ?? null,
            'poster_url' => $posterUrl,
            'image' => $posterUrl,
            'description' => $movie['overview'] ?? '',
        ]);
    }

    private function validatedMovieData(Request $request): array
    {
        // These validation rules protect the database from empty or wrong data.
        return $request->validate([
            'movie_name' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:1'],
            'release_date' => ['required', 'date'],
            'release_place' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:255'],
            'director' => ['required', 'string', 'max:255'],
            'age_rating' => ['required', 'string', 'max:50'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function storeMovieImage(Request $request): ?string
    {
        // If no image was uploaded, return null and keep image empty.
        if (! $request->hasFile('image_file')) {
            return null;
        }

        // Images are stored inside the public/movie-images folder.
        $directory = public_path('movie-images');

        // Create the folder if it does not already exist.
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image_file');
        // Make a unique file name so uploaded images do not overwrite each other.
        $fileName = time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

        // Move the uploaded image file to the public folder.
        $file->move($directory, $fileName);

        // Save this relative path in the database.
        return 'movie-images/'.$fileName;
    }

    private function deleteMovieImage(?string $image): void
    {
        // If the movie has no image, there is nothing to delete.
        if (! $image) {
            return;
        }

        if (str_starts_with($image, 'http')) {
            return;
        }

        // Convert the saved image path into a full public file path.
        $path = public_path($image);

        // Delete the file only if it exists.
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function tmdbRequest(string $path, array $query = [])
    {
        $request = Http::acceptJson()
            ->timeout(10);

        if (config('services.tmdb.token')) {
            $request = $request->withToken(config('services.tmdb.token'));
        } else {
            $query['api_key'] = config('services.tmdb.key');
        }

        return $request->get("https://api.themoviedb.org/3/{$path}", $query);
    }

    private function hasTmdbCredentials(): bool
    {
        return filled(config('services.tmdb.token')) || filled(config('services.tmdb.key'));
    }

    private function tmdbPosterUrl(?string $posterPath): ?string
    {
        if (! $posterPath) {
            return null;
        }

        if (str_starts_with($posterPath, 'http')) {
            return $posterPath;
        }

        return "https://image.tmdb.org/t/p/w500{$posterPath}";
    }

    private function languageName(?string $languageCode): string
    {
        return match ($languageCode) {
            'ar' => 'Arabic',
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
            'it' => 'Italian',
            'de' => 'German',
            'tr' => 'Turkish',
            'hi' => 'Hindi',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            default => strtoupper((string) $languageCode),
        };
    }
}
