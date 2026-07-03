const { formatCurrency, formatMMSS } = require('../../resources/js/utils.js');

describe('formatCurrency', () => {
    test('formats whole numbers with thousands separators', () => {
        expect(formatCurrency(19000)).toBe('$ 19,000');
    });

    test('treats missing/invalid input as zero', () => {
        expect(formatCurrency(undefined)).toBe('$ 0');
        expect(formatCurrency(NaN)).toBe('$ 0');
    });
});

describe('formatMMSS', () => {
    test('pads single-digit minutes and seconds', () => {
        expect(formatMMSS(65)).toBe('01:05');
    });

    test('formats exactly one minute', () => {
        expect(formatMMSS(60)).toBe('01:00');
    });

    test('never goes negative', () => {
        expect(formatMMSS(-5)).toBe('00:00');
    });

    test('formats zero', () => {
        expect(formatMMSS(0)).toBe('00:00');
    });
});
