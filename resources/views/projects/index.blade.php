<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Dashboard</title>
</head>
<body>
    <h1>Cinema Dashboard</h1>
    <p>Welcome, {{ $username }}</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <h2>Movies</h2>

    <form method="GET" action="{{ route('projects.index') }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search movies">
        <button type="submit">Search</button>
        <a href="{{ route('projects.index') }}">Clear</a>
    </form>

    <p>
        <a href="{{ route('projects.create') }}">Add New Movie</a>
    </p>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Movie Name</th>
                <th>Genre</th>
                <th>Duration</th>
                <th>Release Date</th>
                <th>Release Place</th>
                <th>Language</th>
                <th>Director</th>
                <th>Age Rating</th>
                <th>Ticket Price</th>
                <th>Available Seats</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $project)
                <tr>
                    <td>{{ $project->id }}</td>
                    <td>{{ $project->movie_name }}</td>
                    <td>{{ $project->genre }}</td>
                    <td>{{ $project->duration }}</td>
                    <td>{{ $project->release_date->format('Y-m-d') }}</td>
                    <td>{{ $project->release_place }}</td>
                    <td>{{ $project->language }}</td>
                    <td>{{ $project->director }}</td>
                    <td>{{ $project->age_rating }}</td>
                    <td>{{ $project->ticket_price }}</td>
                    <td>{{ $project->available_seats }}</td>
                    <td>{{ $project->description }}</td>
                    <td>
                        <a href="{{ route('projects.edit', $project) }}">Edit</a>

                        <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this movie?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13">No movies found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
