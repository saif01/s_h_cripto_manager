<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public function exptype(){
        // return $this->belongsTo(User::class, 'foreign_key', 'owner_key');
        return $this->belongsTo('App\Models\ExpenseType', 'type_id', 'id');
    }


    public function scopeSearch($query, $val='')
    {
        return $query
        ->where('money', 'LIKE', '%'.$val.'%')
        ->orWhere('details', 'LIKE', '%'.$val.'%')
        ->orWhereHas('exptype', function($query) use ($val){
            $query->WhereRaw('name LIKE ?', '%'.$val.'%');
        });
    }
}
