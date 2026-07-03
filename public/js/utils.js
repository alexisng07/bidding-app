(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.BiddingUtils = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    function formatCurrency(amount) {
        var n = Number(amount) || 0;
        return '$ ' + n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function formatMMSS(totalSeconds) {
        var s = Math.max(0, Math.floor(totalSeconds));
        var mm = String(Math.floor(s / 60)).padStart(2, '0');
        var ss = String(s % 60).padStart(2, '0');
        return mm + ':' + ss;
    }

    return { formatCurrency: formatCurrency, formatMMSS: formatMMSS };
}));
