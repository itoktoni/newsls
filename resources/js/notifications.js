window.addEventListener('new-notification', (e) => {
    const notif = e.detail;
    if (window.showToast) {
        window.showToast(notif.title, notif.body);
    }
});
