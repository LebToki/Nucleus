'use strict';

// Nucleus Service Worker
// Cache name includes the app version so a new release automatically
// invalidates the previous cache (old caches are purged on activate).
const CACHE_NAME = 'nucleus-v1.2.0';

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames
                    .filter(function(name) {
                        return name !== CACHE_NAME;
                    })
                    .map(function(name) {
                        return caches.delete(name);
                    })
            );
        }).then(function() {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function(event) {
    const request = event.request;

    // Only handle GET requests from our own origin
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    // Page navigations: network first, fall back to cached page when offline
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(function(response) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(request, copy);
                    });
                    return response;
                })
                .catch(function() {
                    return caches.match(request);
                })
        );
        return;
    }

    // Static assets: cache first, then network (and cache the result)
    event.respondWith(
        caches.match(request).then(function(cached) {
            if (cached) {
                return cached;
            }
            return fetch(request).then(function(response) {
                if (response.ok) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(request, copy);
                    });
                }
                return response;
            });
        })
    );
});
