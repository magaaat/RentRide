<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestUpdate extends Model
{
    /** @use HasFactory<\Database\Factories\TestUpdateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'plan_id',
    ];
}
