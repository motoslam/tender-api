<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'external_code',
        'number',
        'status',
        'name',
    ];


}
