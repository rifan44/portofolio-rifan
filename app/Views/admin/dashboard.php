<?php
$brandName = trim((string) ($profile['full_name'] ?? '')) ?: 'Portfolio';
$stylesVersion = (string) (filemtime(FCPATH . 'assets/styles.css') ?: 1);
$field = static function (string $name, string $label, mixed $value = '', string $type = 'text', bool $required = true): string {
    $nameEscaped = esc($name, 'attr');
    $labelEscaped = esc($label);
    $valueEscaped = esc((string) ($value ?? ''), 'attr');
    $requiredAttribute = $required ? ' required' : '';
    if ($type === 'textarea') {
        return '<label class="admin-field"><span>' . $labelEscaped . '</span><textarea name="' . $nameEscaped . '"' . $requiredAttribute . '>' . esc((string) ($value ?? '')) . '</textarea></label>';
    }
    return '<label class="admin-field"><span>' . $labelEscaped . '</span><input type="' . esc($type, 'attr') . '" name="' . $nameEscaped . '" value="' . $valueEscaped . '"' . $requiredAttribute . '></label>';
};
$typeLabels = ['education' => 'Pendidikan', 'organization' => 'Organisasi', 'work' => 'Pengalaman kerja', 'achievement' => 'Pencapaian', 'project' => 'Proyek / aktivitas'];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0a0b0f">
  <meta name="robots" content="noindex, nofollow">
  <title>Kelola portofolio — <?= esc($brandName) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/styles.css?v=' . $stylesVersion) ?>">
</head>
<body class="admin-body">
  <header class="admin-topbar"><a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark<?= ! empty($profile['logo_photo']) ? ' brand-mark-image' : '' ?>"><?php if (! empty($profile['logo_photo'])): ?><img class="brand-image" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt=""><?php else: ?>R.<?php endif; ?></span><span class="brand-name"><?= esc($brandName) ?></span></a><div class="admin-top-actions"><a class="button button-small button-ghost" href="<?= site_url('/') ?>">Lihat situs ↗</a><form action="<?= site_url('admin/logout') ?>" method="post"><?= csrf_field() ?><button class="button button-small button-outline" type="submit">Keluar</button></form></div></header>
  <main class="admin-wrap">
    <div class="admin-heading"><div><p class="eyebrow">PORTFOLIO CONTENT MANAGER</p><h1>Kelola konten</h1><p class="admin-muted">Semua data yang kamu simpan di sini dibaca langsung dari database saat portofolio dibuka.</p></div></div>
    <?php if ($message): ?><div class="admin-alert admin-alert-success" role="status"><?= esc($message) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="admin-alert admin-alert-error" role="alert"><strong>Periksa kembali data:</strong><ul><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

    <section class="admin-panel" id="profile"><div class="admin-section-heading"><div><p class="eyebrow">01 / PROFILE</p><h2>Informasi diri</h2></div></div>
      <form action="<?= site_url('admin/profile') ?>" method="post" class="admin-form" enctype="multipart/form-data"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
        <div class="admin-form-grid">
          <?= $field('full_name', 'Nama lengkap', $profile['full_name']) ?><?= $field('role', 'Peran / profesi', $profile['role']) ?>
          <?= $field('headline', 'Headline utama', $profile['headline'], 'text', true) ?><?= $field('email', 'Email kontak', $profile['email'], 'email') ?>
          <?= $field('summary', 'Ringkasan', $profile['summary'], 'textarea') ?><?= $field('about', 'Tentang saya', $profile['about'], 'textarea') ?>
          <?= $field('location', 'Lokasi', $profile['location'], 'text', false) ?><?= $field('focus', 'Fokus saat ini', $profile['focus'], 'text', false) ?>
          <?= $field('availability', 'Status / availability', $profile['availability'], 'text', false) ?>
        </div>
        <div class="admin-upload-grid">
          <div class="admin-upload-field"><label class="admin-field"><span>Foto profil</span><input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WebP; maksimal 2 MB.</small></label><?php if (! empty($profile['profile_photo'])): ?><img class="admin-image-preview admin-profile-preview" src="<?= esc(site_url('media/' . $profile['profile_photo']), 'attr') ?>" alt="Foto profil saat ini"><label class="admin-checkbox"><input type="checkbox" name="remove_profile_photo" value="1"><span>Hapus foto profil</span></label><?php endif; ?></div>
          <div class="admin-upload-field"><label class="admin-field"><span>Logo / ikon R.</span><input type="file" name="logo_photo" accept="image/jpeg,image/png,image/webp"><small>Gambar ini menggantikan tanda R. di header dan footer.</small></label><?php if (! empty($profile['logo_photo'])): ?><img class="admin-image-preview admin-logo-preview" src="<?= esc(site_url('media/' . $profile['logo_photo']), 'attr') ?>" alt="Logo saat ini"><label class="admin-checkbox"><input type="checkbox" name="remove_logo_photo" value="1"><span>Kembali ke logo R.</span></label><?php endif; ?></div>
        </div>
        <button class="button button-primary" type="submit">Simpan profil</button>
      </form>
    </section>

    <section class="admin-panel" id="projects"><div class="admin-section-heading"><div><p class="eyebrow">02 / PROJECTS</p><h2>Proyek</h2></div></div>
      <?php foreach ($projects as $projectIndex => $project): ?>
        <div class="admin-record-item"><details class="admin-record"><summary><strong><?= esc($project['title']) ?></strong><span><?= esc($project['category_label']) ?> · <?= esc($project['year']) ?></span></summary>
          <form action="<?= site_url('admin/projects/save') ?>" method="post" class="admin-form" enctype="multipart/form-data"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"><input type="hidden" name="id" value="<?= esc($project['id'], 'attr') ?>">
            <div class="admin-upload-field"><label class="admin-field"><span>Gambar pratinjau</span><input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"><small>Unggah tangkapan layar atau gambar proyek; JPG, PNG, atau WebP maksimal 2 MB.</small></label><?php if (! empty($project['cover_image'])): ?><img class="admin-image-preview admin-project-preview" src="<?= esc(site_url('media/' . $project['cover_image']), 'attr') ?>" alt="Gambar pratinjau <?= esc($project['title'], 'attr') ?>"><label class="admin-checkbox"><input type="checkbox" name="remove_cover_image" value="1"><span>Hapus gambar pratinjau</span></label><?php endif; ?></div>
            <div class="admin-form-grid">
              <?= $field('year', 'Tahun', $project['year']) ?>
              <label class="admin-field"><span>Kategori</span><select name="category" required><?php foreach (['web' => 'Web app', 'game' => 'Game development', 'infra' => 'Infrastructure'] as $value => $label): ?><option value="<?= esc($value, 'attr') ?>" <?= $project['category'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
              <?= $field('status', 'Status', $project['status']) ?><?= $field('category_label', 'Label kategori', $project['category_label']) ?><?= $field('title', 'Nama proyek', $project['title']) ?>
              <?= $field('description', 'Deskripsi singkat', $project['description'], 'textarea') ?><?= $field('details', 'Deskripsi detail', $project['details'], 'textarea', false) ?>
              <?= $field('stack', 'Teknologi (pisahkan dengan koma)', implode(', ', $project['stack']), 'text', false) ?><?= $field('demo_url', 'URL live preview', $project['demo_url'], 'url', false) ?><?= $field('source_url', 'URL source code', $project['source_url'], 'url', false) ?>
            </div><button class="button button-primary" type="submit">Simpan proyek</button>
          </form>
          <form action="<?= site_url('admin/projects/delete/' . $project['id']) ?>" method="post" class="admin-delete-form" onsubmit="return confirm('Hapus proyek ini?')"><?= csrf_field() ?><button class="button button-small button-danger" type="submit">Hapus proyek</button></form>
        </details>
          <div class="admin-record-actions"><span>Urutan <?= $projectIndex + 1 ?></span><div><form action="<?= site_url('admin/projects/reorder/' . $project['id']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="direction" value="up"><button class="button button-small button-outline admin-order-button" type="submit" aria-label="Naikkan proyek <?= esc($project['title'], 'attr') ?>" <?= $projectIndex === 0 ? 'disabled' : '' ?>>&#8593;</button></form><form action="<?= site_url('admin/projects/reorder/' . $project['id']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="direction" value="down"><button class="button button-small button-outline admin-order-button" type="submit" aria-label="Turunkan proyek <?= esc($project['title'], 'attr') ?>" <?= $projectIndex === count($projects) - 1 ? 'disabled' : '' ?>>&#8595;</button></form></div></div>
        </div>
      <?php endforeach; ?>
      <details class="admin-record" id="add-project"><summary><strong>＋ Tambah proyek</strong><span>Isi detail proyek baru</span></summary>
        <form action="<?= site_url('admin/projects/save') ?>" method="post" class="admin-form" enctype="multipart/form-data"><?= csrf_field() ?><div class="admin-upload-field"><label class="admin-field"><span>Gambar pratinjau (opsional)</span><input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WebP; maksimal 2 MB.</small></label></div><div class="admin-form-grid">
          <?= $field('year', 'Tahun', date('Y')) ?>
          <label class="admin-field"><span>Kategori</span><select name="category" required><option value="web">Web app</option><option value="game">Game development</option><option value="infra">Infrastructure</option></select></label>
          <?= $field('status', 'Status', 'IN PROGRESS') ?><?= $field('category_label', 'Label kategori', 'SELECTED PROJECT') ?><?= $field('title', 'Nama proyek') ?>
          <?= $field('description', 'Deskripsi singkat', '', 'textarea') ?><?= $field('details', 'Deskripsi detail', '', 'textarea', false) ?>
          <?= $field('stack', 'Teknologi (pisahkan dengan koma)', '', 'text', false) ?><?= $field('demo_url', 'URL live preview', '', 'url', false) ?><?= $field('source_url', 'URL source code', '', 'url', false) ?>
        </div><button class="button button-primary" type="submit">Tambah proyek</button></form>
      </details>
    </section>

    <section class="admin-panel" id="skills"><div class="admin-section-heading"><div><p class="eyebrow">03 / TOOLKIT</p><h2>Keahlian & teknologi</h2></div></div>
      <div class="admin-chip-list"><?php foreach ($skills as $skill): ?><form action="<?= site_url('admin/skills/delete/' . $skill['id']) ?>" method="post" class="admin-chip-form"><?= csrf_field() ?><span><?= esc($skill['name']) ?></span><button type="submit" aria-label="Hapus <?= esc($skill['name'], 'attr') ?>">×</button></form><?php endforeach; ?></div>
      <form action="<?= site_url('admin/skills/save') ?>" method="post" class="admin-inline-form"><?= csrf_field() ?><?= $field('name', 'Tambah skill', '', 'text') ?><button class="button button-primary" type="submit">Tambah</button></form>
    </section>

    <section class="admin-panel" id="experience"><div class="admin-section-heading"><div><p class="eyebrow">04 / THE JOURNEY</p><h2>Pendidikan, pengalaman & pencapaian</h2></div></div>
      <?php foreach ($experiences as $experienceIndex => $experience): ?>
        <div class="admin-record-item"><details class="admin-record"><summary><strong><?= esc($experience['title']) ?></strong><span><?= esc($experience['date_range']) ?> · <?= esc($typeLabels[$experience['type']] ?? $experience['type']) ?></span></summary>
          <form action="<?= site_url('admin/experiences/save') ?>" method="post" class="admin-form"><?= csrf_field() ?><input type="hidden" name="id" value="<?= esc($experience['id'], 'attr') ?>"><div class="admin-form-grid">
            <label class="admin-field"><span>Jenis</span><select name="type" required><?php foreach ($typeLabels as $value => $label): ?><option value="<?= esc($value, 'attr') ?>" <?= $experience['type'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
            <?= $field('date_range', 'Periode', $experience['date_range']) ?><?= $field('title', 'Jabatan / aktivitas', $experience['title']) ?><?= $field('organization', 'Institusi / organisasi', $experience['organization']) ?><?= $field('description', 'Deskripsi', $experience['description'], 'textarea') ?>
          </div><button class="button button-primary" type="submit">Simpan</button></form>
          <form action="<?= site_url('admin/experiences/delete/' . $experience['id']) ?>" method="post" class="admin-delete-form" onsubmit="return confirm('Hapus riwayat ini?')"><?= csrf_field() ?><button class="button button-small button-danger" type="submit">Hapus riwayat</button></form>
        </details>
          <div class="admin-record-actions"><span>Urutan <?= $experienceIndex + 1 ?></span><div><form action="<?= site_url('admin/experiences/reorder/' . $experience['id']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="direction" value="up"><button class="button button-small button-outline admin-order-button" type="submit" aria-label="Naikkan <?= esc($experience['title'], 'attr') ?>" <?= $experienceIndex === 0 ? 'disabled' : '' ?>>&#8593;</button></form><form action="<?= site_url('admin/experiences/reorder/' . $experience['id']) ?>" method="post"><?= csrf_field() ?><input type="hidden" name="direction" value="down"><button class="button button-small button-outline admin-order-button" type="submit" aria-label="Turunkan <?= esc($experience['title'], 'attr') ?>" <?= $experienceIndex === count($experiences) - 1 ? 'disabled' : '' ?>>&#8595;</button></form></div></div>
        </div>
      <?php endforeach; ?>
      <details class="admin-record"><summary><strong>＋ Tambah riwayat</strong><span>Pendidikan, organisasi, kerja, pencapaian, atau proyek</span></summary>
        <form action="<?= site_url('admin/experiences/save') ?>" method="post" class="admin-form"><?= csrf_field() ?><div class="admin-form-grid">
          <label class="admin-field"><span>Jenis</span><select name="type" required><?php foreach ($typeLabels as $value => $label): ?><option value="<?= esc($value, 'attr') ?>"><?= esc($label) ?></option><?php endforeach; ?></select></label>
          <?= $field('date_range', 'Periode') ?><?= $field('title', 'Jabatan / aktivitas') ?><?= $field('organization', 'Institusi / organisasi') ?><?= $field('description', 'Deskripsi', '', 'textarea') ?>
        </div><button class="button button-primary" type="submit">Tambah riwayat</button></form>
      </details>
    </section>

    <section class="admin-panel" id="contact"><div class="admin-section-heading"><div><p class="eyebrow">05 / CONTACT</p><h2>Social links</h2></div></div>
      <div class="admin-social-list"><?php foreach ($socialLinks as $social): ?><div><strong><?= esc($social['platform']) ?></strong><a href="<?= esc($social['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($social['url']) ?></a><form action="<?= site_url('admin/social-links/delete/' . $social['id']) ?>" method="post"><?= csrf_field() ?><button class="button button-small button-danger" type="submit">Hapus</button></form></div><?php endforeach; ?></div>
      <form action="<?= site_url('admin/social-links/save') ?>" method="post" class="admin-inline-form"><?= csrf_field() ?><?= $field('platform', 'Nama platform', '') ?><?= $field('url', 'URL profil', '', 'url') ?><button class="button button-primary" type="submit">Tambah link</button></form>
    </section>
    <p class="admin-footer-note">Data tersimpan di database project ini. Jangan masukkan password atau credential database ke konten portofolio.</p>
  </main>
</body>
</html>
