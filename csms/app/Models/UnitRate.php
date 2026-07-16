<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitRate extends Model
{
    protected $fillable = ['item_key', 'item_name', 'unit', 'rate'];
    protected $casts = ['rate' => 'decimal:2'];
}
