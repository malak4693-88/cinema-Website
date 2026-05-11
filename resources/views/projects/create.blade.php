<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie</title>
</head>
<body>
    <h1>Add Movie</h1>
    <p>Welcome, {{ $username }}</p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        @include('projects.form', ['project' => null])

        <button type="submit">Save</button>
        <a href="{{ route('projects.index') }}">Back</a>
    </form>
</body>
</html>
