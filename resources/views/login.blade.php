<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Login</title>
</head>
<body>
    <h1>Cinema Login</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}">
        </div>

        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password">
        </div>

        <button type="submit">Login</button>
    </form>
</body>
</html>
