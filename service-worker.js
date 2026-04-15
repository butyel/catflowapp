const CACHE_NAME = 'catflow-v3';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
    '/',
    '/index.php',
    '/register.php',
    '/dashboard.php',
    '/gatos.php',
    '/saude.php',
    '/financeiro.php',
    '/adocoes.php',
    '/perfil.php',
    '/manifest.json'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(ASSETS_TO_CACHE).catch(() => {});
            })
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    
    if (url.pathname.includes('/api/')) return;
    
    event.respondWith(
        fetch(event.request)
            .then(res => {
                const resClone = res.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, resClone));
                return res;
            })
            .catch(() => {
                if (event.request.mode === 'navigate') {
                    return caches.match(OFFLINE_URL).then(res => res || caches.match('/'));
                }
                return caches.match(event.request);
            })
    );
});

self.addEventListener('push', event => {
    let data = { title: 'CATFLOW', body: 'Nova notificação', icon: '/assets/icons/icon-192x192.png' };
    
    try {
        if (event.data) {
            data = { ...data, ...event.data.json() };
        }
    } catch (e) {
        data.body = event.data ? event.data.text() : data.body;
    }
    
    const options = {
        body: data.body,
        icon: data.icon || '/assets/icons/icon-192x192.png',
        badge: '/assets/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' },
        actions: [
            { action: 'open', title: 'Abrir' },
            { action: 'dismiss', title: 'Dispensar' }
        ],
        requireInteraction: data.urgent || false,
        tag: data.tag || 'catflow-notif',
        renotify: true
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.action === 'dismiss') return;
    
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(windowClients => {
                for (const client of windowClients) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(urlToOpen);
                        return client.focus();
                    }
                }
                return clients.openWindow(urlToOpen);
            })
    );
});

self.addEventListener('notificationclose', event => {
    console.log('Notificação dispensada:', event.notification.tag);
});

self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (event.data?.type === 'CLEAR_CACHE') {
        caches.delete(CACHE_NAME);
    }
});
