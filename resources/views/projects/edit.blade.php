<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
</head>
<body>
    <h1>Edit Movie</h1>
    <p>Welcome, {{ $username }}</p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        @include('projects.form', ['project' => $project])

        <button type="submit">Update</button>
        <a href="{{ route('projects.index') }}">Back</a>
    </form>
</body>
</html>
