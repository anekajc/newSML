/*
 * PWA + Web Push untuk permintaan otorisasi (dimuat layout newmasterTest).
 * Konfigurasi dari window.OTO_PUSH (ditulis layout): vapidKey, swUrl, subscribeUrl,
 * unsubscribeUrl, countUrl, token.
 *
 * - Daftarkan public/sw.js.
 * - #otoPushToggle (ikon lonceng dicoret di header) muncul selama perangkat ini belum
 *   berlangganan; izin notifikasi HARUS diminta dari tap pengguna (syarat iOS/Chrome).
 * - Badge #otoBellCount di-poll dari /otorisasicount tiap 60 detik.
 * - window.OtoPush dipakai juga oleh halaman otorisasi/inbox.blade.php.
 */
(function () {
    var cfg = window.OTO_PUSH || {};

    function supported() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    function isIos() {
        return /iphone|ipad|ipod/i.test(navigator.userAgent);
    }

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    }

    function urlBase64ToUint8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var raw = atob(base64);
        var arr = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
        return arr;
    }

    function post(url, data) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': cfg.token, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        }).then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        });
    }

    var registration = supported() ? navigator.serviceWorker.register(cfg.swUrl) : null;

    function getSubscription() {
        return registration.then(function (reg) { return reg.pushManager.getSubscription(); });
    }

    // Status perangkat ini: 'unsupported' | 'ios-need-install' | 'denied' | 'off' | 'on'
    function status() {
        if (!supported()) {
            return Promise.resolve(isIos() && !isStandalone() ? 'ios-need-install' : 'unsupported');
        }
        if (Notification.permission === 'denied') return Promise.resolve('denied');
        return getSubscription().then(function (sub) { return sub ? 'on' : 'off'; });
    }

    function subscribe() {
        if (!registration) return Promise.reject(new Error('Browser ini tidak mendukung notifikasi push'));
        if (!cfg.vapidKey) return Promise.reject(new Error('VAPID key belum di-set di server'));
        return Notification.requestPermission().then(function (perm) {
            if (perm !== 'granted') throw new Error('Izin notifikasi ditolak');
            return registration;
        }).then(function (reg) {
            return reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(cfg.vapidKey)
            });
        }).then(function (sub) {
            return post(cfg.subscribeUrl, sub.toJSON());
        }).then(refreshToggle);
    }

    function unsubscribe() {
        if (!registration) return Promise.resolve();
        return getSubscription().then(function (sub) {
            if (!sub) return;
            return post(cfg.unsubscribeUrl, { endpoint: sub.endpoint }).then(function () {
                return sub.unsubscribe();
            });
        }).then(refreshToggle);
    }

    function refreshToggle() {
        var btn = document.getElementById('otoPushToggle');
        return status().then(function (st) {
            if (btn) btn.style.display = (st === 'off') ? '' : 'none';
            // Perangkat yang sudah berlangganan: kirim ulang ke server (idempoten) supaya
            // langganan tetap terikat ke user yang sedang login di browser ini.
            if (st === 'on') {
                getSubscription().then(function (sub) { if (sub) post(cfg.subscribeUrl, sub.toJSON()).catch(function () {}); });
            }
            document.dispatchEvent(new CustomEvent('otopush:status', { detail: st }));
            return st;
        });
    }

    function refreshCount() {
        if (!cfg.countUrl) return;
        fetch(cfg.countUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (res) {
                if (!res) return;
                var n = (res.pending || 0) + (res.hasil || 0);
                var el = document.getElementById('otoBellCount');
                if (el) {
                    el.textContent = n > 99 ? '99+' : n;
                    el.style.display = n ? '' : 'none';
                }
                // Panel Beranda mobile (newmaster) ikut mendengarkan angka yang sama.
                document.dispatchEvent(new CustomEvent('otopush:count', { detail: res }));
                if ('setAppBadge' in navigator) {
                    (n ? navigator.setAppBadge(n) : navigator.clearAppBadge()).catch(function () {});
                }
            })
            .catch(function () {});
    }

    window.OtoPush = {
        status: status,
        subscribe: subscribe,
        unsubscribe: unsubscribe,
        refreshCount: refreshCount,
        isIos: isIos,
        isStandalone: isStandalone
    };

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('otoPushToggle');
        if (btn) {
            btn.addEventListener('click', function () {
                subscribe().then(function () {
                    if (window.alertify) alertify.success('Notifikasi aktif di perangkat ini');
                }).catch(function (e) {
                    if (window.alertify) alertify.warning(e.message);
                });
            });
        }
        refreshToggle();
        refreshCount();
        setInterval(refreshCount, 60000);
    });
})();
