<div>
    <label for="movie_name">Movie Name</label>
    <input id="movie_name" type="text" name="movie_name" value="{{ old('movie_name', $project->movie_name ?? '') }}">
</div>

<div>
    <label for="genre">Genre</label>
    <input id="genre" type="text" name="genre" value="{{ old('genre', $project->genre ?? '') }}">
</div>

<div>
    <label for="duration">Duration</label>
    <input id="duration" type="number" name="duration" value="{{ old('duration', $project->duration ?? '') }}">
</div>

<div>
    <label for="release_date">Release Date</label>
    <input id="release_date" type="date" name="release_date" value="{{ old('release_date', isset($project) ? $project->release_date->format('Y-m-d') : '') }}">
</div>

<div>
    <label for="release_place">Release Place</label>
    <input id="release_place" type="text" name="release_place" value="{{ old('release_place', $project->release_place ?? '') }}">
</div>

<div>
    <label for="language">Language</label>
    <input id="language" type="text" name="language" value="{{ old('language', $project->language ?? '') }}">
</div>

<div>
    <label for="director">Director</label>
    <input id="director" type="text" name="director" value="{{ old('director', $project->director ?? '') }}">
</div>

<div>
    <label for="age_rating">Age Rating</label>
    <input id="age_rating" type="text" name="age_rating" value="{{ old('age_rating', $project->age_rating ?? '') }}">
</div>

<div>
    <label for="ticket_price">Ticket Price</label>
    <input id="ticket_price" type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', $project->ticket_price ?? '') }}">
</div>

<div>
    <label for="available_seats">Available Seats</label>
    <input id="available_seats" type="number" name="available_seats" value="{{ old('available_seats', $project->available_seats ?? '') }}">
</div>

<div>
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $project->description ?? '') }}</textarea>
</div>
