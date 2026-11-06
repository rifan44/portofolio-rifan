<?php
$name = (string) $profile['full_name'];
$words = preg_split('/\s+/', trim((string) $profile['headline'])) ?: [];
$firstLine = implode(' ', array_slice($words, 0, 2));
$accent = implode(' ', array_slice($words, 2, 2));
$lastLine = implode(' ', array_slice($words, 4));
$categoryLabels = ['web' => 'Aplikasi web', 'game' => 'Pengembangan game', 'infra' => 'Infrastruktur'];
$experienceTypeLabels = ['education' => 'Pendidikan', 'organization' => 'Organisasi', 'work' => 'Pengalaman kerja', 'achievement' => 'Pencapaian', 'project' => 'Proyek / aktivitas'];
$contactEmail = trim((string) $profile['email']);
$hasContactEmail = filter_var($contactEmail, FILTER_VALIDATE_EMAIL) !== false && ! str_ends_with(strtolower($contactEmail), '@example.com');
$isSafeExternalUrl = static function (string $value): bool {
    if (filter_var($value, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $parts = parse_url($value);
    return is_array($parts)
        && isset($parts['host'])
        && in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true);
};
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0a0b0f">
  <meta name="description" content="<?= esc($name) ?> — <?= esc($profile['role']) ?>">
  <title><?= esc($name) ?> — Portofolio Pribadi</title>
  <link rel="stylesheet" href="<?= base_url('assets/styles.css') ?>">
</head>
<body>
  <div class="scroll-progress" id="scrollProgress"></div>
  <div class="ambient ambient-one" aria-hidden="true"></div>
  <div class="ambient ambient-two" aria-hidden="true"></div>

  <header class="site-header" id="top">
    <nav class="nav-shell container" aria-label="Navigasi utama">
      <a class="brand" href="#top" aria-label="Kembali ke beranda"><span class="brand-mark<?= ! empty($profile['logo_photo']) ? ' brand-mark-image' : '' ?>"><?php if (! empty($profile['logo_photo'])): ?><img class="brand-image" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt=""><?php else: ?>R.<?php endif; ?></span><span class="brand-name"><?= esc($name) ?></span></a>
      <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Buka navigasi" aria-expanded="false"><span></span><span></span></button>
      <div class="nav-links" id="navLinks">
        <a href="#about">Tentang</a><a href="#work">Proyek</a><a href="#experience">Pengalaman</a><a href="#contact">Kontak</a>
        <a class="button button-small button-outline" href="<?= site_url('admin/login') ?>">Admin</a>
      </div>
    </nav>
  </header>

  <main>
    <section class="hero container section-space" id="home">
      <div class="hero-copy reveal">
        <?php if (trim((string) $profile['availability']) !== ''): ?><div class="availability"><span class="pulse-dot"></span><span><?= esc($profile['availability']) ?></span></div><?php endif; ?>
        <p class="eyebrow">PORTOFOLIO <span class="eyebrow-line"></span> <?= date('Y') ?></p>
        <h1><?= esc($firstLine) ?><br><span class="headline-accent"><?= esc($accent ?: 'yang sedang belajar') ?></span><?= $lastLine !== '' ? '<br>' . esc($lastLine) : '' ?><span class="accent-dot">.</span></h1>
        <p class="hero-description"><?= esc($profile['summary']) ?></p>
        <div class="hero-actions"><a class="button button-primary" href="#work">Lihat proyek <span class="arrow">↗</span></a><a class="text-link" href="#contact">Lihat kontak <span>↗</span></a></div>
        <div class="hero-meta"><div><span class="meta-label">LOKASI</span><strong><?= esc($profile['location']) ?></strong></div><div><span class="meta-label">MINAT</span><strong><?= esc($profile['focus']) ?></strong></div></div>
      </div>
      <div class="hero-visual reveal reveal-delay" aria-label="Visual ilustrasi kode">
        <div class="visual-orbit orbit-one"></div><div class="visual-orbit orbit-two"></div><div class="visual-glow"></div>
        <div class="code-window"><div class="window-top"><div class="window-dots"><i></i><i></i><i></i></div><span>developer.ts</span><span class="window-more">•••</span></div>
          <div class="code-content" aria-hidden="true">
            <div><span class="line-no">01</span><span class="code-purple">const</span> <span class="code-blue">developer</span> = {</div>
            <div><span class="line-no">02</span>&nbsp;&nbsp;name: <span class="code-green">'<?= esc($name) ?>'</span>,</div>
            <div><span class="line-no">03</span>&nbsp;&nbsp;role: <span class="code-green">'<?= esc($profile['role']) ?>'</span>,</div>
            <div><span class="line-no">04</span>&nbsp;&nbsp;mindset: <span class="code-green">'Terus belajar'</span>,</div>
            <div><span class="line-no">05</span>&nbsp;&nbsp;build: <span class="code-yellow">(idea)</span> <span class="code-purple">=&gt;</span> solution(idea)</div>
            <div><span class="line-no">06</span>};</div><div class="code-cursor-line"><span class="line-no">07</span><span class="typing-cursor"></span></div>
          </div><div class="window-footer"><span><i class="status-dot"></i> Terus belajar</span><span>UTF-8&nbsp; · &nbsp;JavaScript</span></div>
        </div>
        <div class="floating-note note-top"><span class="note-icon">⌘</span><span><b>Belajar</b><small>sambil praktik</small></span></div>
        <div class="floating-note note-bottom"><span class="note-icon note-icon-lime">✦</span><span><b>Membuat</b><small>proyek pribadi</small></span></div>
        <div class="visual-index">01 <span>—</span> 05</div>
      </div>
      <div class="hero-bottomline"><span>GULIR UNTUK MELIHAT</span><div class="scroll-line"><i></i></div><span>01 / PERKENALAN</span></div>
    </section>



    <section class="about-section container section-space" id="about">
      <div class="section-heading reveal"><div><p class="eyebrow">01 / TENTANG SAYA</p><h2><?= esc($profile['role']) ?>.</h2></div></div>
      <div class="about-grid">
        <div class="about-card profile-card reveal"><div class="profile-card-top"><span class="tiny-label">PROFIL SINGKAT</span><span class="profile-card-star">✳</span></div><div class="avatar-block"><?php if (! empty($profile['profile_photo'])): ?><img class="profile-photo" src="<?= esc(site_url('media/' . $profile['profile_photo']), 'attr') ?>" alt="Foto profil <?= esc($name, 'attr') ?>"><?php else: ?><span><?= esc(implode('', array_map(static fn ($part) => mb_substr($part, 0, 1), array_slice(preg_split('/\s+/', trim($name)) ?: [], 0, 2))) ?: 'R') ?></span><?php endif; ?><div class="avatar-orbit"></div></div>
          <h3><?= esc($name) ?></h3><p class="role-text"><?= esc($profile['role']) ?></p><div class="profile-card-bottom"><span><?= esc(strtoupper($profile['location'] ?: 'INDONESIA')) ?></span><span class="profile-card-year">MAHASISWA</span></div>
        </div>
        <div class="about-copy reveal reveal-delay"><p class="large-copy"><?= esc($profile['about']) ?></p><div class="about-facts"><div><strong><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?></strong><span>PROYEK<br>SAYA</span></div><div><strong><?= str_pad((string) count($skills), 2, '0', STR_PAD_LEFT) ?></strong><span>KEAHLIAN<br>SAYA</span></div><div><strong><?= str_pad((string) count($experiences), 2, '0', STR_PAD_LEFT) ?></strong><span>RIWAYAT<br>SAYA</span></div></div></div>
      </div>
    </section>

    <section class="work-section section-space" id="work"><div class="container">
      <div class="section-heading reveal"><div><p class="eyebrow">02 / PROYEK</p><h2>Beberapa proyek<br><span class="serif-accent">yang saya kerjakan.</span></h2></div><p class="section-aside">Proyek kuliah dan eksperimen pribadi.</p></div>
      <div class="project-toolbar reveal"><div class="filter-list" id="projectFilters"><button class="filter-button active" data-filter="all">Semua <span><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?></span></button><?php foreach ($categoryLabels as $category => $label): ?><button class="filter-button" data-filter="<?= esc($category, 'attr') ?>"><?= esc($label) ?></button><?php endforeach; ?></div><span class="project-sort-label">DAFTAR PROYEK <span>↘</span></span></div>
      <div class="project-grid" id="projectGrid">
        <?php foreach ($projects as $index => $project): ?>
          <?php $detailData = ['title' => $project['title'], 'categoryLabel' => $project['category_label'], 'year' => $project['year'], 'details' => $project['details'] ?: $project['description'], 'stack' => $project['stack'], 'demo' => $project['demo_url'], 'source' => $project['source_url']]; ?>
          <article class="project-card reveal is-visible" data-action="project-detail" data-category="<?= esc($project['category'], 'attr') ?>" data-project="<?= esc(json_encode($detailData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'attr') ?>">
            <div class="project-cover <?= esc($project['category'], 'attr') ?><?= ! empty($project['cover_image']) ? ' has-image' : '' ?>"><?php if (! empty($project['cover_image'])): ?><img class="project-cover-image" src="<?= esc(site_url('media/' . $project['cover_image']), 'attr') ?>" alt="Pratinjau proyek <?= esc($project['title'], 'attr') ?>" loading="lazy"><?php else: ?><div class="cover-grid"></div><?php if ($project['category'] === 'game'): ?><div class="cover-game-shape"></div><div class="cover-orb"></div><?php elseif ($project['category'] === 'infra'): ?><div class="cover-server"><span></span><span></span><span></span></div><?php else: ?><div class="cover-window"></div><div class="cover-orb"></div><?php endif; ?><?php endif; ?><span class="project-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?> / PROYEK</span><span class="project-category"><?= esc($project['category_label']) ?></span><button class="project-open" type="button" aria-label="Lihat detail <?= esc($project['title'], 'attr') ?>"><span aria-hidden="true">&#8599;</span></button></div>
            <div class="project-info"><div class="project-topline"><span class="project-status"><?= esc($project['status']) ?></span><span class="project-year"><?= esc($project['year']) ?></span></div><h3><?= esc($project['title']) ?></h3><p><?= esc($project['description']) ?></p><div class="project-tags"><?php foreach (array_slice($project['stack'], 0, 4) as $technology): ?><span><?= esc($technology) ?></span><?php endforeach; ?></div></div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="work-footnote"><span>PROYEK YANG SAYA KERJAKAN</span><span><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?> <i>—</i> TOTAL PROYEK</span></div>
    </div></section>

    <section class="skills-section container section-space" id="skills"><div class="section-heading reveal"><div><p class="eyebrow">03 / KEAHLIAN</p><h2>Keahlian dan teknologi<br><span class="serif-accent">yang saya pelajari.</span></h2></div><p class="section-aside">Daftar teknologi yang sedang dipelajari.</p></div>
      <div class="skills-layout"><div class="skills-intro reveal"><span class="skills-symbol">⌘</span><h3>Belajar<br>sambil praktik.</h3><p>Teknologi yang pernah saya gunakan atau sedang saya pelajari.</p><span class="skills-caption">DAFTAR KEAHLIAN <i>✳</i></span></div><div class="skill-cloud reveal reveal-delay"><?php foreach ($skills as $skill): ?><span class="skill-pill"><?= esc($skill['name']) ?></span><?php endforeach; ?></div></div>
    </section>

    <section class="experience-section section-space" id="experience"><div class="container"><div class="section-heading reveal"><div><p class="eyebrow">04 / PENDIDIKAN & PENGALAMAN</p><h2>Pendidikan dan<br><span class="serif-accent">pengalaman.</span></h2></div><p class="section-aside">Riwayat pendidikan, kegiatan, dan proyek yang pernah saya ikuti.</p></div>
      <div class="timeline"><?php foreach ($experiences as $experience): ?><article class="timeline-item reveal"><div class="timeline-date"><?= esc($experience['date_range']) ?></div><div class="timeline-content"><h3><?= esc($experience['title']) ?></h3><div class="timeline-org"><?= esc($experience['organization']) ?> ? <?= esc($experienceTypeLabels[$experience['type']] ?? $experience['type']) ?></div><p><?= esc($experience['description']) ?></p></div></article><?php endforeach; ?></div>
    </div></section>

    <section class="contact-section container section-space" id="contact"><div class="contact-panel reveal"><div class="contact-orb orb-left"></div><div class="contact-orb orb-right"></div><div class="contact-content"><p class="eyebrow">05 / KONTAK</p><h2>Hubungi saya.</h2><p class="contact-copy"><?= $hasContactEmail ? 'Silakan hubungi saya melalui email atau tautan berikut.' : 'Tautan kontak yang tersedia ada di bawah ini.' ?></p><?php if ($hasContactEmail): ?><a class="button button-primary contact-cta" href="mailto:<?= esc($contactEmail, 'attr') ?>">Kirim email <span class="arrow">↗</span></a><?php endif; ?><div class="contact-socials"><?php foreach ($socialLinks as $social): ?><?php if (! $isSafeExternalUrl((string) $social['url'])) { continue; } ?><a href="<?= esc($social['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($social['platform']) ?> ↗</a><?php endforeach; ?></div></div><div class="contact-side-note">PORTOFOLIO PRIBADI <span>↗</span></div></div></section>
  </main>

  <footer class="site-footer container"><a class="brand" href="#top"><span class="brand-mark<?= ! empty($profile['logo_photo']) ? ' brand-mark-image' : '' ?>"><?php if (! empty($profile['logo_photo'])): ?><img class="brand-image" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt=""><?php else: ?>R.<?php endif; ?></span><span class="brand-name"><?= esc($name) ?></span></a><span>PORTOFOLIO PRIBADI</span><span>© <span id="currentYear"><?= date('Y') ?></span> <?= esc(strtoupper($name)) ?></span></footer>

  <div class="modal-backdrop" id="projectModal" aria-hidden="true"><div class="project-dialog" role="dialog" aria-modal="true" aria-labelledby="projectModalTitle"><button class="modal-close" data-action="close-project-modal" aria-label="Tutup">×</button><div class="detail-label" id="projectModalLabel"></div><h2 id="projectModalTitle"></h2><p class="detail-description" id="projectModalDescription"></p><div class="detail-stack" id="projectModalStack"></div><div class="detail-links" id="projectModalLinks"></div></div></div>
  <button class="back-to-top" id="backToTop" type="button" aria-label="Kembali ke atas" title="Kembali ke atas"><span aria-hidden="true">&#8593;</span></button>
  <script src="<?= base_url('assets/app.js') ?>" defer></script>
</body>
</html>
