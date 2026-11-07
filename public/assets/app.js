(() => {
  'use strict';

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const modal = $('#projectModal');
  let previousFocus = null;

  function safeExternalUrl(value) {
    if (typeof value !== 'string' || value.trim() === '') return '';
    try {
      const url = new URL(value, window.location.href);
      return ['http:', 'https:'].includes(url.protocol) ? url.href : '';
    } catch {
      return '';
    }
  }

  function openProject(card) {
    let project;
    try {
      project = JSON.parse(card.dataset.project || '{}');
    } catch {
      return;
    }

    previousFocus = document.activeElement;
    if (previousFocus === document.body && card.tabIndex >= 0) {
      card.focus();
      previousFocus = card;
    }
    $('#projectModalLabel').textContent = [project.categoryLabel, project.year].filter(Boolean).join(` ${modal.dataset.projectSeparator || '·'} `);
    $('#projectModalTitle').textContent = project.title || '';
    $('#projectModalDescription').textContent = project.details || '';
    const stack = $('#projectModalStack');
    stack.replaceChildren(...(Array.isArray(project.stack) ? project.stack : []).map((technology) => {
      const item = document.createElement('span');
      item.textContent = technology;
      return item;
    }));

    const links = $('#projectModalLinks');
    links.replaceChildren();
    [['demo', modal.dataset.liveLabel], ['source', modal.dataset.sourceLabel]].forEach(([key, label]) => {
      const href = safeExternalUrl(project[key]);
      if (!href) return;
      const link = document.createElement('a');
      link.href = href;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      link.textContent = `${label} ↗`;
      links.append(link);
    });
    if (!links.childElementCount) {
      const empty = document.createElement('span');
      empty.className = 'disabled-link';
      empty.textContent = modal.dataset.emptyLinksLabel || '';
      links.append(empty);
    }

    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    $('.modal-close').focus();
  }

  function closeProject() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (previousFocus instanceof HTMLElement) previousFocus.focus();
  }

  document.addEventListener('click', (event) => {
    const card = event.target.closest('[data-action="project-detail"]');
    if (card) openProject(card);
    if (event.target.closest('[data-action="close-project-modal"]') || event.target === modal) closeProject();

    if (event.target.closest('#backToTop')) {
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      window.scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' });
    }

    const filter = event.target.closest('[data-filter]');
    if (filter) {
      const selected = filter.dataset.filter;
      $$('#projectFilters [data-filter]').forEach((button) => button.classList.toggle('active', button === filter));
      $$('#projectGrid .project-card').forEach((project) => {
        project.hidden = selected !== 'all' && project.dataset.category !== selected;
      });
    }

    if (event.target.closest('#mobileMenuToggle')) {
      const links = $('#navLinks');
      const expanded = links.classList.toggle('open');
      const toggle = $('#mobileMenuToggle');
      toggle.setAttribute('aria-expanded', String(expanded));
      toggle.setAttribute('aria-label', expanded ? toggle.dataset.closeLabel : toggle.dataset.openLabel);
    }
    if (event.target.closest('#navLinks a')) {
      $('#navLinks').classList.remove('open');
      const toggle = $('#mobileMenuToggle');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', toggle.dataset.openLabel);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal.classList.contains('open')) closeProject();
    if (event.key === 'Tab' && modal.classList.contains('open')) {
      const focusable = $$('a[href], button:not(:disabled), [tabindex="0"]', modal).filter((element) => element.offsetParent !== null);
      if (focusable.length) {
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (! event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    }
    if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.project-card')) {
      event.preventDefault();
      openProject(event.target);
    }
  });

  const revealItems = $$('.reveal');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    }), { threshold: 0.12 });
    revealItems.forEach((item) => observer.observe(item));
  } else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  }

  window.addEventListener('scroll', () => {
    const maximum = document.documentElement.scrollHeight - window.innerHeight;
    $('#scrollProgress').style.width = `${maximum > 0 ? (window.scrollY / maximum) * 100 : 0}%`;
    $('#backToTop')?.classList.toggle('is-visible', window.scrollY > 380);
  }, { passive: true });
})();
