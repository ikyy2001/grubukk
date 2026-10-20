/* Grubukk — Essential UI behaviour (no framework) */
(function () {
  'use strict';

  /* ---------- Theme toggle ---------- */
  var root = document.documentElement;
  var stored = null;
  try { stored = localStorage.getItem('grubukk-theme'); } catch (e) {}
  if (stored) root.setAttribute('data-theme', stored);

  function currentTheme() {
    var attr = root.getAttribute('data-theme');
    if (attr) return attr;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function paintThemeIcons() {
    var dark = currentTheme() === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
      btn.innerHTML = dark
        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>'
        : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>';
      btn.setAttribute('aria-label', dark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
    });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-theme-toggle]');
    if (!btn) return;
    var next = currentTheme() === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    try { localStorage.setItem('grubukk-theme', next); } catch (err) {}
    paintThemeIcons();
  });

  paintThemeIcons();

  /* ---------- Sidebar drawer ---------- */
  var hamburger = document.querySelector('[data-sidebar-toggle]');
  var overlay = document.querySelector('.sidebar-overlay');
  if (hamburger) {
    hamburger.addEventListener('click', function () {
      document.body.classList.toggle('sidebar-open');
    });
  }
  if (overlay) {
    overlay.addEventListener('click', function () {
      document.body.classList.remove('sidebar-open');
    });
  }

  /* ---------- Confirm modal ----------
     Any form with data-confirm opens the shared modal; the form is
     submitted only after the user confirms. */
  var backdrop = document.querySelector('.modal-backdrop');
  var pendingForm = null;

  function closeModal() {
    if (backdrop) backdrop.classList.remove('open');
    pendingForm = null;
  }

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.matches('form[data-confirm]')) return;
    if (form.dataset.confirmed === '1') return; // already confirmed
    e.preventDefault();
    pendingForm = form;
    if (!backdrop) { form.submit(); return; }
    var text = backdrop.querySelector('.modal__text');
    if (text) text.textContent = form.getAttribute('data-confirm');
    backdrop.classList.add('open');
    var ok = backdrop.querySelector('[data-modal-ok]');
    if (ok) ok.focus();
  });

  document.addEventListener('click', function (e) {
    if (!backdrop) return;
    if (e.target.closest('[data-modal-ok]')) {
      if (pendingForm) {
        pendingForm.dataset.confirmed = '1';
        pendingForm.submit();
      }
      closeModal();
    } else if (e.target.closest('[data-modal-cancel]') || e.target === backdrop) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
  });

  /* ---------- Flash alerts auto-dismiss ---------- */
  document.querySelectorAll('.alert[data-autohide]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 200ms ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 220);
    }, 5000);
  });
})();
