<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'note',
        'customer_id',
        'payment_method',
        'total_price',
        'paid_amount',
        'change_amount',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
