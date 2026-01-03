<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    public function user(){
        // return $this->belongsTo(User::class, 'foreign_key', 'owner_key');
        return $this->belongsTo('App\Models\User', 'id', 'id');
    }

    public function scopeSearch($query, $val='')
    {
        return $query
        ->where('money', 'LIKE', '%'.$val.'%')
        ->orWhere('details', 'LIKE', '%'.$val.'%')
        ->orWhere('type', 'LIKE', '%'.$val.'%');
    }
}
