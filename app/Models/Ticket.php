<?php

namespace App\Models;

use App\Traits\ShortIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, ShortIdTrait;

    public $incrementing = false;

    const TYPES = ['task', 'bug'];

    const LABELS = ['To Do', 'Doing'];

    protected $fillable = [
        'title',
        'type',
        'description',
        'label',
        'priority',
        'project_id',
        'created_by',
    ];

    public static function boot()
    {
        parent::boot();
        self::generateId();
        self::creating(function ($model) {
            $biggestPriority = Ticket::where('label', $model->label)->orderBy('priority', 'desc')->first();
            $model->priority = $biggestPriority->priority + 1;
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsToMany(User::class, AssignedUser::class, 'ticket_id', 'user_id');
    }

    public function search($params)
    {
        $query = $this::select();

        if (isset($params['project_id']))
            $query->where('project_id', $params['project_id']);

        if (isset($params['label']))
            $query->where('label', $params['label']);

        if (isset($params['type']))
            $query->where('type', $params['type']);

        if (isset($params['created_by']))
            $query->where('created_by', $params['created_by']);

        if (isset($params['assigned_to']))
            $query->whereHas('assignedTo', function ($q) use ($params) {
                $q->where('user_id', $params['assigned_to']);
            });
        
        if (isset($params['title']))
            $query->where('title', 'like', '%' . $params['title'] . '%');

        return $query->get();
    }

    public function isAssigned($user_id)
    {
        return $this->assignedTo()->where('user_id', $user_id)->exists();
    }
}
