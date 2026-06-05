'use strict';

const STATIC_CACHE = 'luxury-static-v5';
const PAGE_CACHE = 'luxury-pages-v5';
const PDP_CACHE = 'luxury-pdp-v5';

const SHELL_FILES = ['/offline.html'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) =>
        Promise.all(SHELL_FILES.map((u) => cache.add(u).catch(() => null)))
      )
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter((k) => ![STATIC_CACHE, PAGE_CACHE, PDP_CACHE].includes(k))
            .map((k) => caches.delete(k))
        )
      )
      .then(() => self.clients.claim())
  );
});

self.addEventListener('message', (event) => {
  const msg = event.data;
  if (!msg || msg.type !== 'CACHE_PDP_ROUTE' || typeof msg.payload !== 'string') {
    return;
  }

  var path = msg.payload.trim();
  if (!path.startsWith('/') || path.length > 520) return;

  var target = self.location.origin + path;

  event.waitUntil(
    caches.open(PDP_CACHE).then((cache) =>
      fetch(new Request(target, { credentials: 'include', mode: 'same-origin' }))
        .then((res) => {
          if (res && res.ok) return cache.put(target, res.clone());
        })
        .catch(() => {})
    )
  );
});

self.addEventListener('fetch', (event) => {
  var incoming = event.request;
  if (incoming.method !== 'GET') return;

  var reqUrl = new URL(incoming.url);
  if (reqUrl.origin !== self.location.origin) return;

  if (incoming.mode === 'navigate') {
    event.respondWith(
      fetch(incoming)
        .then((fresh) => {
          if (fresh && fresh.ok) {
            var copy = fresh.clone();
            caches.open(PAGE_CACHE).then((c) => c.put(incoming, copy).catch(() => {}));
          }
          return fresh;
        })
        .catch(() =>
          caches.match(incoming).then(function (hit) {
            return (
              hit ||
              caches.match('/offline.html').then(function (off) {
                return (
                  off ||
                  new Response('<!DOCTYPE html><html><body><p>Offline</p></body></html>', {
                    headers: { 'Content-Type': 'text/html;charset=UTF-8' },
                  })
                );
              })
            );
          })
        )
    );
    return;
  }

  event.respondWith(
    caches.match(incoming).then(function (cached) {
      if (cached) {
        return cached;
      }
      return fetch(incoming)
        .then(function (res) {
          if (
            res &&
            res.ok &&
            res.type !== 'opaque' &&
            res.status === 200
          ) {
            var dup = res.clone();
            caches
              .open(STATIC_CACHE)
              .then(function (c) {
                return c.put(incoming, dup);
              })
              .catch(function () {});
          }
          return res;
        })
        .catch(function () {
          return (
            caches.match(incoming) ||
            new Response('', { status: 504, statusText: 'Offline' })
          );
        });
    })
  );
});
