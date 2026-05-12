<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
</head>
<body class="site-body dashboard-body">
    <main class="form-page">
        <section class="movie-form-card">
            <p class="eyebrow">Cinema Dashboard</p>
            <h1>Edit Movie</h1>
            <p>Welcome, {{ $username }}</p>

            @if ($errors->any())
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form class="movie-form" method="POST" action="{{ route('movies.update', $movie) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('movies.form', ['movie' => $movie])

                <div class="form-actions">
                    <button class="solid-button" type="submit">Update</button>
                    <a class="outline-button" href="{{ route('dashboard', ['access' => $dashboardAccess]) }}">Back</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
