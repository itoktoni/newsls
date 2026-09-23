import "./forms.js";

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const userId = document.querySelector('meta[name="user-id"]')?.content;
const notificationEnabled = document.querySelector('meta[name="notification-enabled"]')?.content === 'true';

if (notificationEnabled && userId) {
    const { Centrifuge } = await import('centrifuge');

    async function fetchToken(channel) {
        const body = channel ? JSON.stringify({ channel }) : undefined;
        const res = await fetch('/centrifugo/token', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body,
        });
        const data = await res.json();
        return data.token;
    }

    const wsUrl = document.querySelector('meta[name="centrifugo-url"]')?.content || 'ws://localhost:8000/connection/websocket';

    const centrifuge = new Centrifuge(wsUrl, {
        getToken: () => fetchToken(),
    });

    const channel = centrifuge.newSubscription(`notifications#${userId}`, {
        getToken: () => fetchToken(`notifications#${userId}`),
    });

    channel.on('publication', (ctx) => {
        window.dispatchEvent(new CustomEvent('new-notification', { detail: ctx.data }));
    });

    channel.subscribe();
    centrifuge.connect();
    window.centrifuge = centrifuge;
}

/**
 * Format angka: 1.000 (tanpa desimal jika 0), 1,255 / 1,24 (hapus trailing zero).
 * Tanda: titik ribuan, koma desimal.
 */
function formatQty(value) {
    const num = parseFloat(value);
    if (isNaN(num)) return '0';
    const integer = Math.floor(num);
    const decimal = num - integer;
    if (Math.abs(decimal) < 0.001) {
        return integer.toLocaleString('id-ID');
    }
    let formatted = num.toLocaleString('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
    formatted = formatted.replace(/0+$/, '').replace(/,$/, '');
    return formatted;
}
