<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiddingTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $overrides = [])
    {
        return Product::factory()->create(array_merge([
            'starting_price' => 1000,
            'min_increment' => 100,
        ], $overrides));
    }

    public function test_status_before_any_bid_shows_starting_price_and_no_countdown(): void
    {
        $product = $this->makeProduct();

        $response = $this->getJson(route('product.status', $product));

        $response->assertOk()
            ->assertJson([
                'status' => 'before',
                'current_amount' => 1000,
            ]);
    }

    public function test_first_bid_must_meet_or_exceed_starting_price(): void
    {
        $product = $this->makeProduct(['starting_price' => 1000]);

        $response = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Ali',
            'amount' => 500,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('bids', 0);
    }

    public function test_first_valid_bid_starts_the_sixty_second_countdown(): void
    {
        $product = $this->makeProduct(['starting_price' => 1000]);

        $response = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Ali',
            'amount' => 1000,
        ]);

        $response->assertOk()->assertJson(['status' => 'during']);

        $product->refresh();
        $this->assertNotNull($product->bidding_ends_at);
        $this->assertEqualsWithDelta(60, now()->diffInSeconds($product->bidding_ends_at), 2);
    }

    public function test_subsequent_bid_must_beat_current_highest_by_min_increment(): void
    {
        $product = $this->makeProduct(['starting_price' => 1000, 'min_increment' => 100]);
        Bid::factory()->create(['product_id' => $product->id, 'bidder_name' => 'Ali', 'amount' => 1000]);
        $product->update(['bidding_ends_at' => now()->addSeconds(60)]);

        $tooLow = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Abu',
            'amount' => 1050,
        ]);
        $tooLow->assertStatus(422);

        $valid = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Abu',
            'amount' => 1100,
        ]);
        $valid->assertOk()->assertJsonPath('current_amount', 1100);
    }

    public function test_bid_amount_must_be_positive(): void
    {
        $product = $this->makeProduct();

        $response = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Ali',
            'amount' => -50,
        ]);

        $response->assertStatus(422);
    }

    public function test_no_bids_are_accepted_once_bidding_has_ended(): void
    {
        $product = $this->makeProduct();
        Bid::factory()->create(['product_id' => $product->id, 'bidder_name' => 'Ali', 'amount' => 1000]);
        $product->update(['bidding_ends_at' => now()->subSecond()]);

        $response = $this->postJson(route('product.bid', $product), [
            'bidder_name' => 'Abu',
            'amount' => 5000,
        ]);

        $response->assertStatus(422);
    }

    public function test_status_reports_winner_once_bidding_has_ended(): void
    {
        $product = $this->makeProduct();
        Bid::factory()->create(['product_id' => $product->id, 'bidder_name' => 'Ali', 'amount' => 1000]);
        $product->update(['bidding_ends_at' => now()->subSecond()]);

        $response = $this->getJson(route('product.status', $product));

        $response->assertOk()->assertJson([
            'status' => 'end',
            'winner' => 'Ali',
        ]);
    }
}
