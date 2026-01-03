<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cptCoinPrice extends Model
{
    use HasFactory;

    /**
     * Cast attributes to native types
     * Using 'decimal:6' to preserve precision for NUMERIC(25, 6) fields
     */
    protected $casts = [
        'asset_quantity' => 'decimal:6',
        'average_asset_price' => 'decimal:6',
        'total_invested' => 'decimal:6',
        'current_price' => 'decimal:6',
        'current_asset_value' => 'decimal:6',
    ];

    public function scopeSearch($query, $val='')
    {
        return $query
        ->where('asset_name', 'LIKE', '%'.$val.'%')
        ->orWhere('asset_id', 'LIKE', '%'.$val.'%')
        ->orWhere('details', 'LIKE', '%'.$val.'%');
    }
}
