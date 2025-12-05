<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affal extends Model
{
    use HasFactory;

    protected $table = 'affals';
    protected $fillable = ['qty_stock', 'price'];
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public static function getInstance()
    {
        return self::first() ?? self::create(['qty_stock' => 0, 'price' => 0]);
    }
}