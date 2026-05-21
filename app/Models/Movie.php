<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

// These fields are allowed to be inserted or updated using Movie::create() and $movie->update().
#[Fillable([
    'movie_name',
    'genre',
    'duration',
    'release_date',
    'release_place',
    'language',
    'director',
    'age_rating',
    'ticket_price',
    'available_seats',
    'image',
    'description',
])]
class Movie extends Model
{
    protected function casts(): array
    {
        // Cast release_date as a date so Blade can format it with format() and compare it with isFuture().
        return [
            'release_date' => 'date',
            // Cast ticket_price as a decimal with two digits, like 18.00.
            'ticket_price' => 'decimal:2',
        ];
    }
}
