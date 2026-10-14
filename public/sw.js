/*
 * Service worker PWA - HANYA untuk Web Push (notifikasi permintaan otorisasi).
 * Sengaja tanpa cache/offline: data ERP harus selalu live dari server.
 * Payload dari app/Jobs/SendWebPush.php: {title, body, url, tag}.
 * Didaftarkan oleh public/js/otorisasi-push.js.
 */

self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    var data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'AJC', body: event.data ? event.data.text() : '' };
    }

    event.waitUntil(self.registration.showNotification(data.title || 'AJC', {
        body: data.body || '',
        icon: 'img/pwa-192.png',
        badge: 'img/pwa-badge.png',
        tag: data.tag || undefined,
        renotify: !!data.tag,
        data: { url: data.url || 'otorisasiinbox' }
    }));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var target = new URL(event.notification.data.url, self.registration.scope).href;

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
            for (var i = 0; i < list.length; i++) {
                if (list[i].url === target && 'focus' in list[i]) {
                    return list[i].focus();
                }
            }
            return self.clients.openWindow(target);
        })
    );
});
