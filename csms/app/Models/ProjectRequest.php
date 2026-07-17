<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ProjectRequest extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'phone',
        'email',
        'project_type',
        'location',
        'width',
        'height',
        'budget',
        'timeline',
        'requirements',
        'status',
        'assigned_engineer_id',
        'due_date',
    ];

    protected $casts = ['due_date' => 'date', 'width' => 'decimal:2', 'height' => 'decimal:2', 'budget' => 'decimal:2'];

    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function assignedEngineer() { return $this->belongsTo(User::class, 'assigned_engineer_id'); }
    public function technicalReport() { return $this->hasOne(TechnicalReport::class, 'req_id')->latestOfMany('report_id'); }
   
    public function proposals() { return $this->hasMany(Proposal::class, 'project_request_id'); }
    public function latestProposal() { return $this->hasOne(Proposal::class, 'project_request_id')->latestOfMany(); }
}
