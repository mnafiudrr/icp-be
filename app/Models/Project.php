<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, ShortIdTrait;

    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];
}
