<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'type', 'category', 'reference', 'total', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(CashTransactionDetail::class);
    }
}
