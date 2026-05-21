<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
        // Upload the image if one was selected, then save its path in the data array.
        $data['image'] = $this->storeMovieImage($request);

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

        // Only replace the image when the user uploads a new one.
        if ($request->hasFile('image')) {
            // Delete the old image before saving the new image.
            $this->deleteMovieImage($movie->image);
            $data['image'] = $this->storeMovieImage($request);
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
            'image' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function storeMovieImage(Request $request): ?string
    {
        // If no image was uploaded, return null and keep image empty.
        if (! $request->hasFile('image')) {
            return null;
        }

        // Images are stored inside the public/movie-images folder.
        $directory = public_path('movie-images');

        // Create the folder if it does not already exist.
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
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

        // Convert the saved image path into a full public file path.
        $path = public_path($image);

        // Delete the file only if it exists.
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
