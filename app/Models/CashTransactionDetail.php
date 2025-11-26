<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'cash_transaction_details';

    protected $fillable = [
        'cash_transaction_id', 'description', 'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(CashTransaction::class, 'cash_transaction_id');
    }
}
