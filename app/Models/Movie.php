<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
        return [
            'release_date' => 'date',
            'ticket_price' => 'decimal:2',
        ];
    }
}
