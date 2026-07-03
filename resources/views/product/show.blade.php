@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <h1>{{ $product->name }}</h1>
    <p class="product-desc">{{ $product->description }}</p>

    <div class="card" id="bidding-app"
         data-product-id="{{ $product->id }}"
         data-status-url="{{ route('product.status', $product) }}"
         data-bid-url="{{ route('product.bid', $product) }}">

        <div class="countdown-bar" id="countdown-bar">
            <span>&#9200;</span>
            <span id="countdown-text">01:00</span>
        </div>

        <div class="stage" id="stage">
            <div class="label">Please 'Bid' to Start</div>
        </div>

        <div id="error-box" class="error"></div>

        <div class="action-bar">
            <input type="number" id="amount-input" placeholder="Min {{ $product->starting_price }}" step="0.01">
            <input type="text" id="name-input" placeholder="Your name">
            <button type="button" class="btn-increment" id="increment-btn">+100</button>
            <button type="button" class="btn-bid" id="bid-btn">Bid</button>
        </div>
    </div>

    <script src="{{ asset('/js/utils.js') }}"></script>
    <script src="{{ asset('/js/bidding.js') }}"></script>
@endsection
