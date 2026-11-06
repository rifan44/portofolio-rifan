(() => {
  'use strict';

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const modal = $('#projectModal');

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

    $('#projectModalLabel').textContent = `${project.categoryLabel || 'SELECTED PROJECT'} · ${project.year || ''}`;
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
    [['demo', 'Live preview ↗'], ['source', 'Source code ↗']].forEach(([key, label]) => {
      const href = safeExternalUrl(project[key]);
      if (!href) return;
      const link = document.createElement('a');
      link.href = href;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      link.textContent = label;
      links.append(link);
    });
    if (!links.childElementCount) {
      const empty = document.createElement('span');
      empty.className = 'disabled-link';
      empty.textContent = 'Project link belum ditambahkan';
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
  }

  document.addEventListener('click', (event) => {
    const card = event.target.closest('[data-action="project-detail"]');
    if (card) openProject(card);
    if (event.target.closest('[data-action="close-project-modal"]') || event.target === modal) closeProject();

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
      $('#mobileMenuToggle').setAttribute('aria-expanded', String(expanded));
    }
    if (event.target.closest('#navLinks a')) {
      $('#navLinks').classList.remove('open');
      $('#mobileMenuToggle').setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeProject();
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
  }, { passive: true });
})();
