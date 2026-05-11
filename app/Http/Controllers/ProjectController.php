<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        $search = $request->query('search');

        $projects = Project::query()
            ->when($search, function ($query, string $search): void {
                $query->where('movie_name', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('language', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'search' => $search,
            'username' => session('username'),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        return view('projects.create', [
            'username' => session('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        Project::create($this->validatedProjectData($request));

        return redirect()
            ->route('projects.index')
            ->with('success', 'Movie added successfully.');
    }

    public function edit(Project $project): View|RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        return view('projects.edit', [
            'project' => $project,
            'username' => session('username'),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        $project->update($this->validatedProjectData($request));

        return redirect()
            ->route('projects.index')
            ->with('success', 'Movie updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if (! session()->has('username')) {
            return redirect()->route('login.form');
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Movie deleted successfully.');
    }

    private function validatedProjectData(Request $request): array
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
            'description' => ['nullable', 'string'],
        ]);
    }
}
