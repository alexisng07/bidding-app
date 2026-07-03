<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\BidFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'starting_price',
        'min_increment',
        'bidding_ends_at',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'min_increment' => 'decimal:2',
        'bidding_ends_at' => 'datetime',
    ];

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function highestBid(): ?Bid
    {
        // Assumes bids are loaded or will trigger a query; used in places where
        // we already eager-loaded / locked the row, see BiddingController.
        return $this->bids()->orderByDesc('amount')->orderByDesc('id')->first();
    }

    /**
     * Bidding state as understood by the front-end wireframes: before / during / end.
     */
    public function status(): string
    {
        if (is_null($this->bidding_ends_at)) {
            return 'before';
        }

        return now()->greaterThanOrEqualTo($this->bidding_ends_at) ? 'end' : 'during';
    }

    public function secondsRemaining(): int
    {
        if (is_null($this->bidding_ends_at)) {
            return 60; // full countdown, not started yet
        }

        return max(0, now()->diffInSeconds($this->bidding_ends_at, false));
    }
}
