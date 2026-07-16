<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    protected $fillable = [
        'project_request_id',
    'cement_qty', 'cement_cost',
    'sand_qty', 'sand_cost',
    'steel_qty', 'steel_cost',
    'brick_qty', 'brick_cost',
    'mason_qty', 'mason_cost',
    'carpenter_qty', 'carpenter_cost',
    'helper_qty', 'helper_cost',
    'mixer_qty', 'mixer_cost',
    'excavator_qty', 'excavator_cost',
    'truck_qty', 'truck_cost',
    'estimated_duration',
    'remarks',
    'grand_total',
    ];

    protected $casts = ['grand_total' => 'decimal:2'];

    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class, 'project_request_id');
    }
}
