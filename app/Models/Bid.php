<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    /** @use HasFactory<\Database\Factories\BidFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'bidder_name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
