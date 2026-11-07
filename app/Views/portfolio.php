<?php
$name = (string) $profile['full_name'];
$stylesVersion = (string) (filemtime(FCPATH . 'assets/styles.css') ?: 1);
$scriptVersion = (string) (filemtime(FCPATH . 'assets/app.js') ?: 1);
$copy = array_column($siteCopy, 'value', 'copy_key');
$copyText = static fn (string $key): string => (string) ($copy[$key] ?? '');
$formatCopy = static fn (string $key, array $values = []): string => strtr($copyText($key), $values);
$defaultSectionKeys = ['about', 'projects', 'skills', 'experience', 'contact'];
$requestedSectionKeys = array_values(array_unique(array_filter(
    array_column($sectionOrder, 'section_key'),
    static fn ($key): bool => in_array($key, $defaultSectionKeys, true),
)));
$sectionKeys = array_merge($requestedSectionKeys, array_values(array_diff($defaultSectionKeys, $requestedSectionKeys)));
$sectionPositions = array_flip($sectionKeys);
$sectionEyebrow = static function (string $copyKey, string $sectionKey) use ($copyText, $sectionPositions): string {
    $label = $copyText($copyKey);
    $number = str_pad((string) (($sectionPositions[$sectionKey] ?? 0) + 2), 2, '0', STR_PAD_LEFT);

    return preg_replace('/^\s*\d+\s*\/\s*/u', $number . ' / ', $label, 1) ?? $label;
};
$navigationSections = [
    'about' => ['id' => 'about', 'copy' => 'nav_about'],
    'projects' => ['id' => 'work', 'copy' => 'nav_projects'],
    'experience' => ['id' => 'experience', 'copy' => 'nav_experience'],
    'contact' => ['id' => 'contact', 'copy' => 'nav_contact'],
];
$words = preg_split('/\s+/', trim((string) $profile['headline'])) ?: [];
$firstLine = implode(' ', array_slice($words, 0, 2));
$accent = implode(' ', array_slice($words, 2, 2));
$lastLine = implode(' ', array_slice($words, 4));
$categoryLabels = ['web' => $copyText('category_web'), 'game' => $copyText('category_game'), 'infra' => $copyText('category_infra')];
$experienceTypeLabels = [
    'education' => $copyText('experience_type_education'),
    'organization' => $copyText('experience_type_organization'),
    'work' => $copyText('experience_type_work'),
    'achievement' => $copyText('experience_type_achievement'),
    'project' => $copyText('experience_type_project'),
];
$codeContent = strtr($copyText('code_content'), ['{nama}' => $name, '{peran}' => (string) $profile['role']]);
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
  <title><?= esc($name) ?> — <?= esc($copyText('page_title_suffix')) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/styles.css?v=' . $stylesVersion) ?>">
</head>
<body>
  <div class="scroll-progress" id="scrollProgress"></div>
  <div class="ambient ambient-one" aria-hidden="true"></div>
  <div class="ambient ambient-two" aria-hidden="true"></div>

  <header class="site-header" id="top">
    <nav class="nav-shell container" aria-label="<?= esc($copyText('nav_aria_label'), 'attr') ?>">
      <a class="brand" href="#top" aria-label="<?= esc($copyText('home_link_aria'), 'attr') ?>"><span class="brand-mark<?= ! empty($profile['logo_photo']) ? ' brand-mark-image' : '' ?>"><?php if (! empty($profile['logo_photo'])): ?><img class="brand-image" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt=""><?php else: ?>R.<?php endif; ?></span><span class="brand-name"><?= esc($name) ?></span></a>
      <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="<?= esc($copyText('mobile_menu_open_label'), 'attr') ?>" data-open-label="<?= esc($copyText('mobile_menu_open_label'), 'attr') ?>" data-close-label="<?= esc($copyText('mobile_menu_close_label'), 'attr') ?>" aria-expanded="false"><span></span><span></span></button>
      <div class="nav-links" id="navLinks">
        <?php foreach ($sectionKeys as $sectionKey): ?><?php if (isset($navigationSections[$sectionKey])): $navigation = $navigationSections[$sectionKey]; ?><a href="#<?= esc($navigation['id'], 'attr') ?>"><?= esc($copyText($navigation['copy'])) ?></a><?php endif; ?><?php endforeach; ?>
        <a class="button button-small button-outline" href="<?= site_url('admin/login') ?>"><?= esc($copyText('nav_admin')) ?></a>
      </div>
    </nav>
  </header>

  <main>
    <section class="hero container section-space" id="home">
      <div class="hero-copy reveal">
        <?php if (trim((string) $profile['availability']) !== ''): ?><div class="availability"><span class="pulse-dot"></span><span><?= esc($profile['availability']) ?></span></div><?php endif; ?>
        <p class="eyebrow"><?= esc($copyText('hero_eyebrow')) ?> <span class="eyebrow-line"></span> <?= date('Y') ?></p>
        <h1><?= esc($firstLine) ?><br><span class="headline-accent"><?= esc($accent ?: $copyText('headline_accent_fallback')) ?></span><?= $lastLine !== '' ? '<br>' . esc($lastLine) : '' ?><span class="accent-dot">.</span></h1>
        <p class="hero-description"><?= esc($profile['summary']) ?></p>
        <div class="hero-actions"><a class="button button-primary" href="#work"><?= esc($copyText('hero_button_projects')) ?> <span class="arrow" aria-hidden="true">↗</span></a><a class="text-link" href="#contact"><?= esc($copyText('hero_button_contact')) ?> <span aria-hidden="true">↗</span></a></div>
        <div class="hero-meta"><div><span class="meta-label"><?= esc($copyText('hero_location_label')) ?></span><strong><?= esc($profile['location']) ?></strong></div><div><span class="meta-label"><?= esc($copyText('hero_focus_label')) ?></span><strong><?= esc($profile['focus']) ?></strong></div></div>
      </div>
      <div class="hero-visual reveal reveal-delay" aria-label="<?= esc($copyText('hero_visual_aria'), 'attr') ?>">
        <div class="visual-orbit orbit-one"></div><div class="visual-orbit orbit-two"></div><div class="visual-glow"></div>
        <div class="code-window"><div class="window-top"><div class="window-dots"><i></i><i></i><i></i></div><span><?= esc($copyText('code_title')) ?></span><span class="window-more" aria-hidden="true">•••</span></div>
          <pre class="code-content code-content-editable"><?= esc($codeContent) ?></pre><div class="window-footer"><span><i class="status-dot"></i> <?= esc($copyText('code_status')) ?></span><span><?= esc($copyText('code_encoding')) ?></span></div>
        </div>
        <div class="floating-note note-top"><span class="note-icon" aria-hidden="true">⌘</span><span><b><?= esc($copyText('hero_note_learning_title')) ?></b><small><?= esc($copyText('hero_note_learning_subtitle')) ?></small></span></div>
        <div class="floating-note note-bottom"><span class="note-icon note-icon-lime" aria-hidden="true">✦</span><span><b><?= esc($copyText('hero_note_project_title')) ?></b><small><?= esc($copyText('hero_note_project_subtitle')) ?></small></span></div>
        <div class="visual-index"><?= esc($copyText('hero_current_index')) ?> <span><?= esc($copyText('hero_index_separator')) ?></span> <?= str_pad((string) (count($sectionKeys) + 1), 2, '0', STR_PAD_LEFT) ?></div>
      </div>
      <div class="hero-bottomline"><span><?= esc($copyText('hero_scroll_hint')) ?></span><div class="scroll-line"><i></i></div><span><?= esc($copyText('hero_section_index')) ?></span></div>
    </section>



    <?php $sectionMarkup = []; ob_start(); ?>
    <section class="about-section container section-space" id="about">
      <div class="section-heading reveal"><div><p class="eyebrow"><?= esc($sectionEyebrow('about_eyebrow', 'about')) ?></p><h2><?= esc($profile['role']) ?>.</h2></div></div>
      <div class="about-grid">
        <div class="about-card profile-card reveal"><div class="profile-card-top"><span class="tiny-label"><?= esc($copyText('about_profile_label')) ?></span><span class="profile-card-star" aria-hidden="true">✳</span></div><div class="avatar-block"><?php if (! empty($profile['profile_photo'])): ?><img class="profile-photo" src="<?= esc(site_url('media/' . $profile['profile_photo']), 'attr') ?>" alt="<?= esc($formatCopy('profile_photo_alt', ['{nama}' => $name]), 'attr') ?>"><?php else: ?><span><?= esc(implode('', array_map(static fn ($part) => mb_substr($part, 0, 1), array_slice(preg_split('/\s+/', trim($name)) ?: [], 0, 2))) ?: 'R') ?></span><?php endif; ?><div class="avatar-orbit"></div></div>
          <h3><?= esc($name) ?></h3><p class="role-text"><?= esc($profile['role']) ?></p><div class="profile-card-bottom"><span><?= esc(strtoupper($profile['location'] ?: $copyText('about_location_fallback'))) ?></span><span class="profile-card-year"><?= esc($copyText('profile_student_label')) ?></span></div>
        </div>
        <div class="about-copy reveal reveal-delay"><p class="large-copy"><?= esc($profile['about']) ?></p><div class="about-facts"><div><strong><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?></strong><span><?= esc($copyText('about_projects_label')) ?><br><?= esc($copyText('about_projects_tail')) ?></span></div><div><strong><?= str_pad((string) count($skills), 2, '0', STR_PAD_LEFT) ?></strong><span><?= esc($copyText('about_skills_label')) ?><br><?= esc($copyText('about_skills_tail')) ?></span></div><div><strong><?= str_pad((string) count($experiences), 2, '0', STR_PAD_LEFT) ?></strong><span><?= esc($copyText('about_history_label')) ?><br><?= esc($copyText('about_history_tail')) ?></span></div></div></div>
      </div>
    </section>
    <?php $sectionMarkup['about'] = ob_get_clean(); ob_start(); ?>

    <section class="work-section section-space" id="work"><div class="container">
      <div class="section-heading reveal"><div><p class="eyebrow"><?= esc($sectionEyebrow('projects_eyebrow', 'projects')) ?></p><h2><?= esc($copyText('projects_title_first')) ?><br><span class="serif-accent"><?= esc($copyText('projects_title_second')) ?></span></h2></div><p class="section-aside"><?= esc($copyText('projects_aside')) ?></p></div>
      <div class="project-toolbar reveal"><div class="filter-list" id="projectFilters"><button class="filter-button active" data-filter="all"><?= esc($copyText('filter_all')) ?> <span><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?></span></button><?php foreach ($categoryLabels as $category => $label): ?><button class="filter-button" data-filter="<?= esc($category, 'attr') ?>"><?= esc($label) ?></button><?php endforeach; ?></div><span class="project-sort-label"><?= esc($copyText('project_list_label')) ?> <span aria-hidden="true">↘</span></span></div>
      <div class="project-grid" id="projectGrid">
        <?php foreach ($projects as $index => $project): ?>
          <?php $detailData = ['title' => $project['title'], 'categoryLabel' => $project['category_label'], 'year' => $project['year'], 'details' => $project['details'] ?: $project['description'], 'stack' => $project['stack'], 'demo' => $project['demo_url'], 'source' => $project['source_url']]; ?>
          <article class="project-card reveal is-visible" data-action="project-detail" data-category="<?= esc($project['category'], 'attr') ?>" data-project="<?= esc(json_encode($detailData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'attr') ?>" tabindex="0" role="button" aria-haspopup="dialog" aria-label="<?= esc($formatCopy('project_detail_label', ['{proyek}' => $project['title']]), 'attr') ?>">
            <div class="project-cover <?= esc($project['category'], 'attr') ?><?= ! empty($project['cover_image']) ? ' has-image' : '' ?>"><?php if (! empty($project['cover_image'])): ?><img class="project-cover-image" src="<?= esc(site_url('media/' . $project['cover_image']), 'attr') ?>" alt="<?= esc($formatCopy('project_image_alt', ['{proyek}' => $project['title']]), 'attr') ?>" loading="lazy"><?php else: ?><div class="cover-grid"></div><?php if ($project['category'] === 'game'): ?><div class="cover-game-shape"></div><div class="cover-orb"></div><?php elseif ($project['category'] === 'infra'): ?><div class="cover-server"><span></span><span></span><span></span></div><?php else: ?><div class="cover-window"></div><div class="cover-orb"></div><?php endif; ?><?php endif; ?><span class="project-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?> / <?= esc($copyText('project_number_suffix')) ?></span><span class="project-category"><?= esc($project['category_label']) ?></span><span class="project-open" aria-hidden="true"><span>&#8599;</span></span></div>
            <div class="project-info"><div class="project-topline"><span class="project-status"><?= esc($project['status']) ?></span><span class="project-year"><?= esc($project['year']) ?></span></div><h3><?= esc($project['title']) ?></h3><p><?= esc($project['description']) ?></p><div class="project-tags"><?php foreach (array_slice($project['stack'], 0, 4) as $technology): ?><span><?= esc($technology) ?></span><?php endforeach; ?></div></div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="work-footnote"><span><?= esc($copyText('projects_footnote')) ?></span><span><?= str_pad((string) count($projects), 2, '0', STR_PAD_LEFT) ?> <i aria-hidden="true">—</i> <?= esc($copyText('projects_total_suffix')) ?></span></div>
    </div></section>

    <?php $sectionMarkup['projects'] = ob_get_clean(); ob_start(); ?>
    <section class="skills-section container section-space" id="skills"><div class="section-heading reveal"><div><p class="eyebrow"><?= esc($sectionEyebrow('skills_eyebrow', 'skills')) ?></p><h2><?= esc($copyText('skills_title_first')) ?><br><span class="serif-accent"><?= esc($copyText('skills_title_second')) ?></span></h2></div><p class="section-aside"><?= esc($copyText('skills_aside')) ?></p></div>
      <div class="skills-layout"><div class="skills-intro reveal"><span class="skills-symbol" aria-hidden="true">⌘</span><h3><?= esc($copyText('skills_note_first')) ?><br><?= esc($copyText('skills_note_second')) ?></h3><p><?= esc($copyText('skills_description')) ?></p><span class="skills-caption"><?= esc($copyText('skills_caption')) ?> <i aria-hidden="true">✳</i></span></div><div class="skill-cloud reveal reveal-delay"><?php foreach ($skills as $skill): ?><span class="skill-pill"><?= esc($skill['name']) ?></span><?php endforeach; ?></div></div>
    </section>

    <?php $sectionMarkup['skills'] = ob_get_clean(); ob_start(); ?>
    <section class="experience-section section-space" id="experience"><div class="container"><div class="section-heading reveal"><div><p class="eyebrow"><?= esc($sectionEyebrow('experience_eyebrow', 'experience')) ?></p><h2><?= esc($copyText('experience_title_first')) ?><br><span class="serif-accent"><?= esc($copyText('experience_title_second')) ?></span></h2></div><p class="section-aside"><?= esc($copyText('experience_aside')) ?></p></div>
      <div class="timeline"><?php foreach ($experiences as $experience): ?><article class="timeline-item reveal"><div class="timeline-date"><?= esc($experience['date_range']) ?></div><div class="timeline-content"><h3><?= esc($experience['title']) ?></h3><div class="timeline-org"><?= esc($experience['organization']) ?> <?= esc($copyText('experience_separator')) ?> <?= esc($experienceTypeLabels[$experience['type']] ?? $experience['type']) ?></div><p><?= esc($experience['description']) ?></p></div></article><?php endforeach; ?></div>
    </div></section>

    <?php $sectionMarkup['experience'] = ob_get_clean(); ob_start(); ?>
    <section class="contact-section container section-space" id="contact"><div class="contact-panel reveal"><div class="contact-orb orb-left"></div><div class="contact-orb orb-right"></div><div class="contact-content"><p class="eyebrow"><?= esc($sectionEyebrow('contact_eyebrow', 'contact')) ?></p><h2><?= esc($copyText('contact_heading')) ?></h2><p class="contact-copy"><?= esc($copyText($hasContactEmail ? 'contact_email_message' : 'contact_no_email_message')) ?></p><?php if ($hasContactEmail): ?><a class="button button-primary contact-cta" href="mailto:<?= esc($contactEmail, 'attr') ?>"><?= esc($copyText('contact_email_button')) ?> <span class="arrow" aria-hidden="true">↗</span></a><?php endif; ?><div class="contact-socials"><?php foreach ($socialLinks as $social): ?><?php if (! $isSafeExternalUrl((string) $social['url'])) { continue; } ?><a href="<?= esc($social['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($social['platform']) ?> <span aria-hidden="true"><?= esc($copyText('contact_social_arrow')) ?></span></a><?php endforeach; ?></div></div><div class="contact-side-note"><?= esc($copyText('contact_side_note')) ?> <span aria-hidden="true">↗</span></div></div></section>
    <?php
    $sectionMarkup['contact'] = ob_get_clean();
    foreach ($sectionKeys as $sectionKey) {
        echo $sectionMarkup[$sectionKey];
    }
    ?>
  </main>

  <footer class="site-footer container"><a class="brand" href="#top" aria-label="<?= esc($copyText('home_link_aria'), 'attr') ?>"><span class="brand-mark<?= ! empty($profile['logo_photo']) ? ' brand-mark-image' : '' ?>"><?php if (! empty($profile['logo_photo'])): ?><img class="brand-image" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt=""><?php else: ?>R.<?php endif; ?></span><span class="brand-name"><?= esc($name) ?></span></a><span><?= esc($copyText('footer_label')) ?></span><span>© <span id="currentYear"><?= date('Y') ?></span> <?= esc(strtoupper($name)) ?></span></footer>

  <div class="modal-backdrop" id="projectModal" aria-hidden="true" data-live-label="<?= esc($copyText('modal_live_label'), 'attr') ?>" data-source-label="<?= esc($copyText('modal_source_label'), 'attr') ?>" data-empty-links-label="<?= esc($copyText('modal_empty_links'), 'attr') ?>" data-project-separator="<?= esc($copyText('project_modal_separator'), 'attr') ?>"><div class="project-dialog" role="dialog" aria-modal="true" aria-labelledby="projectModalTitle"><button class="modal-close" data-action="close-project-modal" aria-label="<?= esc($copyText('modal_close_label'), 'attr') ?>"><span aria-hidden="true"><?= esc($copyText('close_icon_label')) ?></span></button><div class="detail-label" id="projectModalLabel"></div><h2 id="projectModalTitle"></h2><p class="detail-description" id="projectModalDescription"></p><div class="detail-stack" id="projectModalStack"></div><div class="detail-links" id="projectModalLinks"></div></div></div>
  <button class="back-to-top" id="backToTop" type="button" aria-label="<?= esc($copyText('back_to_top_label'), 'attr') ?>" title="<?= esc($copyText('back_to_top_label'), 'attr') ?>"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none"><path d="M12 19V5m0 0L6.5 10.5M12 5l5.5 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
  <script src="<?= base_url('assets/app.js?v=' . $scriptVersion) ?>" defer></script>
</body>
</html>
