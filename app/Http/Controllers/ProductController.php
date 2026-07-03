<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private const BID_DURATION_SECONDS = 60;

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $highestBid = $product->highestBid();

        return view('product.show', [
            'product' => $product,
            'highestBid' => $highestBid,
        ]);
    }

    public function status(Product $product)
    {
        $highestBid = $product->highestBid();

        return response()->json($this->stateFor($product, $highestBid));
    }

    public function bid(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'bidder_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = DB::transaction(function () use ($request, $product) {
            // Lock the product row so two concurrent requests can't both
            // "win" the check against the current highest bid.
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->first();

            $highestBid = $lockedProduct->highestBid();
            $currentAmount = $highestBid ? (float) $highestBid->amount : (float) $lockedProduct->starting_price;
            $minRequired = $currentAmount + (float) $lockedProduct->min_increment;

            // Bidding hasn't started yet -> any first bid must clear the starting price.
            if (is_null($lockedProduct->bidding_ends_at)) {
                $minRequired = max((float) $lockedProduct->starting_price, 0.01);
            } elseif (now()->greaterThanOrEqualTo($lockedProduct->bidding_ends_at)) {
                return ['error' => 'Bidding has ended for this product.'];
            }

            if ((float) $request->input('amount') < $minRequired) {
                return ['error' => "Bid must be at least {$minRequired}."];
            }

            $bid = Bid::create([
                'product_id' => $lockedProduct->id,
                'bidder_name' => $request->input('bidder_name'),
                'amount' => $request->input('amount'),
            ]);

            if (is_null($lockedProduct->bidding_ends_at)) {
                $lockedProduct->bidding_ends_at = now()->addSeconds(self::BID_DURATION_SECONDS);
                $lockedProduct->save();
            }

            return ['bid' => $bid, 'product' => $lockedProduct->fresh()];
        });

        if (isset($result['error'])) {
            return response()->json(['errors' => ['amount' => [$result['error']]]], 422);
        }

        return response()->json($this->stateFor($result['product'], $result['bid']));
    }

    private function stateFor(Product $product, ?Bid $highestBid): array
    {
        $status = $product->status();

        return [
            'status' => $status,
            'seconds_remaining' => $product->secondsRemaining(),
            'current_amount' => $highestBid ? (float) $highestBid->amount : (float) $product->starting_price,
            'current_bidder' => $highestBid?->bidder_name,
            'min_next_bid' => ($highestBid ? (float) $highestBid->amount : (float) $product->starting_price)
                + ($highestBid ? (float) $product->min_increment : 0),
            'winner' => $status === 'end' ? $highestBid?->bidder_name : null,
        ];
    }
}
