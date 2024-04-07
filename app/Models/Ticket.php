<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, ShortIdTrait;

    public $incrementing = false;

    protected $fillable = [
        'title',
        'type',
        'assigned_to',
        'description',
        'label',
        'project_id',
        'created_by',
    ];
}
