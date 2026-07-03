(function () {
    var root = document.getElementById('bidding-app');
    if (!root) return;

    var productId = root.dataset.productId;
    var statusUrl = root.dataset.statusUrl;
    var bidUrl = root.dataset.bidUrl;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    var countdownBar = document.getElementById('countdown-bar');
    var countdownText = document.getElementById('countdown-text');
    var stage = document.getElementById('stage');
    var amountInput = document.getElementById('amount-input');
    var nameInput = document.getElementById('name-input');
    var incrementBtn = document.getElementById('increment-btn');
    var bidBtn = document.getElementById('bid-btn');
    var errorBox = document.getElementById('error-box');

    var INCREMENT_STEP = 100;
    var pollTimer = null;
    var lastKnownBidder = null;

    function render(state) {
        countdownText.textContent = BiddingUtils.formatMMSS(state.seconds_remaining);
        countdownBar.classList.toggle('ended', state.status === 'end');

        if (state.status === 'before') {
            stage.innerHTML = '<div class="label">Please \'Bid\' to Start</div>';
        } else if (state.status === 'during') {
            var byYou = state.current_bidder && nameInput.value && state.current_bidder === nameInput.value;
            stage.innerHTML =
                '<div class="amount">' + BiddingUtils.formatCurrency(state.current_amount) + '</div>' +
                '<div class="label">Current Bid by ' + (byYou ? 'You' : escapeHtml(state.current_bidder || '')) + '</div>';
        } else {
            var youWon = state.winner && nameInput.value && state.winner === nameInput.value;
            stage.innerHTML = youWon
                ? '<div class="winner">You are the winner</div>'
                : '<div class="winner">Won by \'' + escapeHtml(state.winner || 'no one') + '\'</div>';
        }

        amountInput.placeholder = 'Min ' + state.min_next_bid;
        bidBtn.disabled = state.status === 'end';
        incrementBtn.disabled = state.status === 'end';
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function fetchStatus() {
        fetch(statusUrl)
            .then(function (res) { return res.json(); })
            .then(function (state) {
                render(state);
                if (state.status === 'end' && pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            })
            .catch(function () { /* transient network error, ignore this tick */ });
    }

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(fetchStatus, 1000);
    }

    incrementBtn.addEventListener('click', function () {
        var current = parseFloat(amountInput.value) || 0;
        amountInput.value = current + INCREMENT_STEP;
    });

    bidBtn.addEventListener('click', function () {
        errorBox.textContent = '';
        var payload = {
            bidder_name: nameInput.value.trim(),
            amount: parseFloat(amountInput.value),
        };

        if (!payload.bidder_name || !payload.amount || payload.amount <= 0) {
            errorBox.textContent = 'Enter your name and a positive bid amount.';
            return;
        }

        fetch(bidUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then(function (res) { return res.json().then(function (body) { return { ok: res.ok, body: body }; }); })
            .then(function (result) {
                if (!result.ok) {
                    var firstError = Object.values(result.body.errors || {})[0];
                    errorBox.textContent = firstError ? firstError[0] : 'Could not place bid.';
                    return;
                }
                render(result.body);
                startPolling();
            })
            .catch(function () {
                errorBox.textContent = 'Network error, please try again.';
            });
    });

    fetchStatus();
    startPolling();
})();
