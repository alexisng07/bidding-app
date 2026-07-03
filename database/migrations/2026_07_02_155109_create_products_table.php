<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            // Starting price must always be a positive number (enforced at app level too).
            $table->decimal('starting_price', 12, 2);
            // Minimum amount a new bid must exceed the current highest bid by.
            $table->decimal('min_increment', 12, 2)->default(100);
            // Null until the first bid is placed. Once set, bidding closes at this instant.
            $table->timestamp('bidding_ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
