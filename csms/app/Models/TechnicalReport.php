<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalReport extends Model
{
    use HasFactory;

    protected $table = 'technical_reports';

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'req_id',
        'length',
        'width',
        'area',
        'cost_estimate_file',
        'measurement_details',
        'material_cost',
        'labor_cost',
        'equipment_cost',
        'total_budget',
        'duration',
        'estimated_duration',
        'recommendations',
        'remarks',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'area' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'equipment_cost' => 'decimal:2',
        'total_budget' => 'decimal:2',
    ];

    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class, 'req_id', 'id');
    }
}
