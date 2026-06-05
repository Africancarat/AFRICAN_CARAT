document.addEventListener('DOMContentLoaded', () => {
  const nav = document.querySelector('.navbar');
  if (nav) {
    const onScroll = () =>
      nav.classList.toggle('scrolled', window.scrollY > 60);

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }
});

function openFilterSheet() {
  document.getElementById('filterSheet')?.classList.add('open');
  document.getElementById('filterBackdrop')?.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeFilterSheet() {
  document.getElementById('filterSheet')?.classList.remove('open');
  document.getElementById('filterBackdrop')?.classList.remove('open');
  document.body.style.overflow = '';
}

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    const swMeta = document.querySelector('meta[name="pwa-sw-url"]');
    const swUrl =
      (swMeta && swMeta.getAttribute('content')) ||
      '/core/public/sw.js';
    navigator.serviceWorker
      .register(swUrl)
      .then((r) => console.log('[PWA] SW registered:', r.scope))
      .catch((e) => console.warn('[PWA] SW failed:', e));
  });
}

let luxuryDeferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  luxuryDeferredPrompt = e;
  const b = document.getElementById('luxuryPwaBanner');
  if (b && !sessionStorage.getItem('luxuryPwaDismiss')) {
    b.classList.add('is-visible');
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('luxuryPwaInstallBtn');
  if (!btn) return;

  btn.addEventListener('click', async () => {
    const b = document.getElementById('luxuryPwaBanner');
    if (!luxuryDeferredPrompt) {
      b?.classList.remove('is-visible');
      return;
    }
    luxuryDeferredPrompt.prompt();
    try {
      await luxuryDeferredPrompt.userChoice;
    } catch (_) {}
    luxuryDeferredPrompt = null;
    b?.classList.remove('is-visible');
  });

  /** Cache PDP route for stale-offline revisit (paired with sw.js CACHE_PDP). */
  if (
    navigator.serviceWorker &&
    document.body?.dataset.cachePdp === '1'
  ) {
    const path = `${location.pathname}${location.search}`;
    navigator.serviceWorker.ready.then((reg) =>
      reg.active?.postMessage({ type: 'CACHE_PDP_ROUTE', payload: path })
    );
  }
});

/**
 * Wishlist price-drop push requires VAPID keys + Laravel notification storage.
 */
if (typeof window !== 'undefined' && 'Notification' in window) {
  window.luxuryRequestWishlistPush = async () => {
    console.info('[PWA] Push: configure VAPID + server subscriptions to enable alerts.');
    return false;
  };
}
