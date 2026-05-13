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
        return $this->dashboardView($request);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedMovieData($request);
        $data['image'] = $this->storeMovieImage($request);

        Movie::create($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie added successfully.');
    }

    public function edit(Request $request, Movie $movie): View
    {
        return $this->dashboardView($request, $movie);
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $data = $this->validatedMovieData($request);

        if ($request->hasFile('image')) {
            $this->deleteMovieImage($movie->image);
            $data['image'] = $this->storeMovieImage($request);
        }

        $movie->update($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        $this->deleteMovieImage($movie->image);
        $movie->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Movie deleted successfully.');
    }

    private function validatedMovieData(Request $request): array
    {
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

    private function dashboardView(Request $request, ?Movie $editingMovie = null): View
    {
        $search = $request->query('search');

        $movies = Movie::query()
            ->when($search, function ($query, string $search): void {
                $query->where('movie_name', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('language', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $totalMovies = Movie::count();
        $releasedMovies = Movie::whereDate('release_date', '<=', now()->toDateString())->count();
        $unreleasedMovies = Movie::whereDate('release_date', '>', now()->toDateString())->count();

        return view('movies.index', [
            'movies' => $movies,
            'search' => $search,
            'username' => session('username'),
            'totalMovies' => $totalMovies,
            'releasedMovies' => $releasedMovies,
            'unreleasedMovies' => $unreleasedMovies,
            'editingMovie' => $editingMovie,
        ]);
    }

    private function storeMovieImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $directory = public_path('movie-images');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $fileName = time().'-'.uniqid().'.'.$file->getClientOriginalExtension();

        $file->move($directory, $fileName);

        return 'movie-images/'.$fileName;
    }

    private function deleteMovieImage(?string $image): void
    {
        if (! $image) {
            return;
        }

        $path = public_path($image);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
