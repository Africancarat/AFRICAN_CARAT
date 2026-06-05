/**
 * iJewel-style PDP media: viewer / image toggle, renderMetal(), Owl bridge.
 * Does not touch pricing or cart.
 */
(function (global) {
  'use strict';

  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }

  function qsa(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function normalizeMetalToken(str) {
    return String(str || '')
      .toUpperCase()
      .replace(/\s+/g, ' ')
      .trim();
  }

  function formatMetalLabel(key) {
    return String(key || '').replace(/\s+GOLD$/i, ' Gold');
  }

  function resolveMetalKey(label, metalImages, defaultMetal) {
    var desired = normalizeMetalToken(label);
    if (!desired) {
      return defaultMetal;
    }
    var keys = Object.keys(metalImages || {});
    for (var i = 0; i < keys.length; i++) {
      if (normalizeMetalToken(keys[i]) === desired) {
        return keys[i];
      }
    }
    return defaultMetal;
  }

  function isValidUrl(url) {
    url = String(url || '').trim();
    if (!url) return false;
    if (/^https?:\/\//i.test(url)) return true;
    return url.indexOf('/storage/') >= 0 || url.indexOf('/images/') >= 0;
  }

  function filterValidUrls(list) {
    if (!Array.isArray(list)) return [];
    return list.filter(isValidUrl);
  }

  function buildOwlItemHtml(url, withZoom) {
    if (withZoom) {
      return (
        '<div class="item"><div class="lux-zoom-wrap" data-lux-zoom>' +
        '<img src="' + escapeHtml(url) + '" loading="lazy" alt="" class="lux-main-img"></div></div>'
      );
    }
    return '<div class="item"><img src="' + escapeHtml(url) + '" loading="lazy" alt=""></div>';
  }

  function ijMediaActive() {
    return !!qs('[data-product-media].pdp-product-media--ij-active');
  }

  function setGalleryImages(imgList) {
    imgList = filterValidUrls(imgList);
    if (!imgList.length) return false;

    if (typeof global.__pdpSetGalleryImages === 'function') {
      return global.__pdpSetGalleryImages(imgList);
    }

    var gallery = qs('#productGallery .product-details-slider');
    if (!gallery) return false;

    var hasZoom = !!qs('[data-lux-zoom]', qs('#productGallery'));
    var html = imgList.map(function (u) {
      return buildOwlItemHtml(u, hasZoom);
    });

    if (!ijMediaActive()) {
      var legacyThumbs = qs('[data-lux-thumbs]');
      if (legacyThumbs) {
        legacyThumbs.innerHTML = imgList
          .map(function (u) {
            return (
              '<button type="button" class="lux-thumb" data-lux-thumb>' +
              '<img src="' + escapeHtml(u) + '" class="gallery-thumb" loading="lazy" alt="" decoding="async"></button>'
            );
          })
          .join('');
      }
    }

    if (global.jQuery && global.jQuery.fn && global.jQuery.fn.owlCarousel) {
      var $owl = global.jQuery(gallery);
      if ($owl.hasClass('owl-loaded')) {
        $owl.trigger('replace.owl.carousel', [html.join('')]);
        $owl.trigger('refresh.owl.carousel');
        if (typeof global.__luxPdpGalleryInit === 'function') {
          global.__luxPdpGalleryInit();
        }
        return true;
      }
    }

    gallery.innerHTML = html.join('');
    if (typeof global.__luxPdpGalleryInit === 'function') {
      global.__luxPdpGalleryInit();
    }
    return true;
  }

  function setMainImageSrc(src) {
    if (!isValidUrl(src)) return;
    if (typeof global.__pdpSetMainImageSrc === 'function') {
      global.__pdpSetMainImageSrc(src);
    }
  }

  function createPdpMedia() {
    var mediaRoot = qs('[data-product-media]');
    if (!mediaRoot) {
      return null;
    }

    var metalImages = {};
    try {
      metalImages = JSON.parse(mediaRoot.getAttribute('data-metal-images') || '{}');
    } catch (e) {
      metalImages = {};
    }

    Object.keys(metalImages).forEach(function (key) {
      metalImages[key] = filterValidUrls(metalImages[key]);
    });

    var defaultMetal = mediaRoot.getAttribute('data-default-metal') || 'YELLOW GOLD';
    var hasViewer = mediaRoot.getAttribute('data-has-viewer') === '1';
    var viewerUrl = mediaRoot.getAttribute('data-viewer-url') || '';
    var placeholder = mediaRoot.getAttribute('data-thumb-placeholder') || '';

    var viewer = qs('[data-media-viewer]', mediaRoot);
    var imageWrap = qs('[data-media-image-wrap]', mediaRoot);
    var image = qs('[data-media-image]', mediaRoot);
    var thumbs = qs('[data-metal-thumbs]', mediaRoot);
    var label = qs('[data-metal-label]', mediaRoot);
    var empty = qs('[data-metal-empty]', mediaRoot);
    var galleryWrap = qs('[data-metal-gallery]', mediaRoot);
    var fallback = qs('[data-media-fallback]', mediaRoot);

    var thumbBtnClass = 'pdp-media-thumb';

    function viewerThumbHtml(active) {
      if (!hasViewer) return '';
      return (
        '<button type="button" class="' + thumbBtnClass + ' pdp-media-thumb--viewer' +
        (active ? ' is-active' : '') +
        '" data-media-thumb data-mode="viewer" aria-label="View 3D model" role="listitem">' +
        '<span class="pdp-media-thumb__viewer-inner" aria-hidden="true">' +
        '<svg class="pdp-media-thumb__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">' +
        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/>' +
        '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M4 7.5l8 4.5 8-4.5M4 16.5l8 4.5 8-4.5"/>' +
        '</svg><span class="pdp-media-thumb__badge">3D</span></span></button>'
      );
    }

    function imageThumbHtml(url, active) {
      var safe = escapeHtml(url);
      var ph = escapeHtml(placeholder);
      return (
        '<button type="button" class="' + thumbBtnClass + ' pdp-media-thumb--image' +
        (active ? ' is-active' : '') +
        '" data-media-thumb data-mode="image" data-url="' + safe +
        '" aria-label="View product image" role="listitem">' +
        '<img src="' + safe + '" alt="" class="pdp-media-thumb__img" loading="lazy" decoding="async"' +
        (ph ? ' onerror="this.onerror=null;this.src=\'' + ph + '\';"' : '') +
        '></button>'
      );
    }

    function ensureStageImage(url) {
      if (!isValidUrl(url)) return;
      if (!image) {
        if (!imageWrap) {
          imageWrap = document.createElement('div');
          imageWrap.className = 'pdp-media-stage__img-wrap';
          imageWrap.setAttribute('data-media-image-wrap', '');
          imageWrap.setAttribute('data-lux-zoom', '');
          var stage = qs('[data-media-stage]', mediaRoot);
          if (stage) stage.appendChild(imageWrap);
        }
        image = document.createElement('img');
        image.setAttribute('data-media-image', '');
        image.className = 'pdp-media-stage__img';
        image.alt = '';
        imageWrap.appendChild(image);
      }
      image.src = url;
      imageWrap.classList.remove('d-none');
      image.classList.remove('d-none'); // <-- ADD THIS
    }

    function setActiveThumb(activeBtn) {
      if (!thumbs) return;
      qsa('[data-media-thumb]', thumbs).forEach(function (btn) {
        btn.classList.toggle('is-active', btn === activeBtn);
      });
    }

    function showViewer() {
      if (!hasViewer || !viewer) return;
      mediaRoot.classList.remove('is-viewer-mode');
      viewer.classList.remove('d-none');
      if (imageWrap) imageWrap.classList.add('d-none');
      if (image) image.classList.add('d-none');
      if (empty) empty.classList.add('d-none');
      if (fallback) fallback.classList.add('d-none');
      var btn = qs('[data-media-thumb][data-mode="viewer"]', thumbs);
      if (btn) setActiveThumb(btn);
    }

    function showImage(url, activeBtn) {
      if (!isValidUrl(url)) return;
      if (viewer) viewer.classList.add('d-none');
      if (fallback) fallback.classList.add('d-none');
      if (empty) empty.classList.add('d-none');
      if (image) {
        image.classList.remove('d-none'); // <-- ADD THIS
      }
      ensureStageImage(url);
      setActiveThumb(activeBtn);
      setMainImageSrc(url);
    }

    function bindThumbs() {
      if (!thumbs) return;
      qsa('[data-media-thumb]', thumbs).forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (btn.getAttribute('data-mode') === 'viewer') {
            showViewer();
            return;
          }
          var url = btn.getAttribute('data-url') || '';
          var urls = [];
          qsa('[data-media-thumb][data-mode="image"]', thumbs).forEach(function (b) {
            var u = b.getAttribute('data-url');
            if (isValidUrl(u)) urls.push(u);
          });
          if (urls.length) setGalleryImages(urls);
          showImage(url, btn);
        });
      });
    }

    function renderMetal(metalKey) {
      var urls = filterValidUrls(metalImages[metalKey] || []);

      if (label) label.textContent = formatMetalLabel(metalKey);
      if (galleryWrap) galleryWrap.classList.toggle('d-none', !hasViewer && urls.length === 0);
      if (!thumbs) return;

      var imageThumbsHtml = urls
        .map(function (url, index) {
          return imageThumbHtml(url, !hasViewer && index === 0);
        })
        .join('');

      thumbs.innerHTML = viewerThumbHtml(hasViewer) + imageThumbsHtml;
      bindThumbs();

      if (hasViewer) {
        showViewer();
      } else if (urls[0]) {
        setGalleryImages(urls);
        var firstBtn = qs('[data-media-thumb][data-mode="image"]', thumbs);
        showImage(urls[0], firstBtn);
      } else {
        if (imageWrap) imageWrap.classList.add('d-none');
        if (viewer) viewer.classList.add('d-none');
        if (empty) empty.classList.remove('d-none');
        if (fallback && viewerUrl) fallback.classList.remove('d-none');
      }
    }

    function renderMetalByLabel(label) {
      renderMetal(resolveMetalKey(label, metalImages, defaultMetal));
    }

    function init() {
      bindThumbs();
      if (hasViewer) {
        showViewer();
      } else {
        renderMetal(defaultMetal);
      }
      if (typeof global.__luxPdpGalleryInit === 'function') {
        global.__luxPdpGalleryInit();
      }
    }

    return {
      init: init,
      renderMetal: renderMetal,
      renderMetalByLabel: renderMetalByLabel,
      showViewer: showViewer,
      showImage: showImage,
      setActiveThumb: setActiveThumb,
    };
  }

  var bootAttempts = 0;

  function boot() {
    var mediaRoot = qs('[data-product-media]');
    if (mediaRoot && typeof global.__pdpSetGalleryImages !== 'function' && bootAttempts < 40) {
      bootAttempts += 1;
      setTimeout(boot, 50);
      return;
    }

    var api = createPdpMedia();
    if (!api) return;
    global.PdpMedia = api;
    api.init();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(typeof window !== 'undefined' ? window : this);
