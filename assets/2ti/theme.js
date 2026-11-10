/* 2TI theme — behaviour. ~2 KB, no dependencies.
   Theme and direction are user preferences, persisted; everything else is
   progressive: the markup works before this file loads. */
(function () {
  'use strict';

  var root = document.documentElement;
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
  };

  /* ---- theme ---------------------------------------------------------- */
  function setTheme(name) {
    root.setAttribute('data-theme', name);
    store.set('2ti.theme', name);
    document.querySelectorAll('[data-theme-toggle]').forEach(function (el) {
      el.textContent = name === 'dark' ? 'Light' : 'Dark';
    });
  }

  /* ---- direction ------------------------------------------------------ */
  function setDir(dir) {
    root.setAttribute('dir', dir);
    root.setAttribute('lang', dir === 'rtl' ? 'ar' : 'en');
    store.set('2ti.dir', dir);
    document.querySelectorAll('[data-dir-toggle]').forEach(function (el) {
      el.textContent = dir === 'rtl' ? 'LTR' : 'RTL';
    });
  }

  var savedTheme = store.get('2ti.theme');
  setTheme(savedTheme || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
  setDir(store.get('2ti.dir') || 'ltr');

  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-theme-toggle],[data-dir-toggle],[data-modal-open],[data-modal-close],[data-tab]');
    if (!t) { return; }

    if (t.hasAttribute('data-theme-toggle')) { setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }
    if (t.hasAttribute('data-dir-toggle')) { setDir(root.getAttribute('dir') === 'rtl' ? 'ltr' : 'rtl'); }

    if (t.hasAttribute('data-modal-open')) {
      var m = document.querySelector(t.getAttribute('data-modal-open'));
      if (m) { m.setAttribute('data-open', 'true'); }
    }
    if (t.hasAttribute('data-modal-close')) {
      var b = t.closest('.modal-backdrop');
      if (b) { b.setAttribute('data-open', 'false'); }
    }
    if (t.hasAttribute('data-tab')) {
      var group = t.closest('[data-tabs]');
      if (group) {
        group.querySelectorAll('[data-tab]').forEach(function (x) { x.setAttribute('aria-selected', 'false'); });
        t.setAttribute('aria-selected', 'true');
      }
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop[data-open="true"]').forEach(function (m) { m.setAttribute('data-open', 'false'); });
    }
  });

  /* ---- toast ---------------------------------------------------------- */
  window.toast = function (message, kind) {
    var stack = document.querySelector('.toast-stack');
    if (!stack) { stack = document.createElement('div'); stack.className = 'toast-stack'; document.body.appendChild(stack); }
    var el = document.createElement('div');
    el.className = 'toast';
    el.style.borderInlineStartColor = kind === 'danger' ? 'var(--danger)' : kind === 'warning' ? 'var(--warning)' : 'var(--success)';
    el.textContent = message;
    stack.appendChild(el);
    setTimeout(function () { el.remove(); }, 4000);
  };
})();
