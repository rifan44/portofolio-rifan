<?php $brandName = trim((string) ($profile['full_name'] ?? '')) ?: 'Portfolio'; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0a0b0f">
  <meta name="robots" content="noindex, nofollow">
  <title>Masuk admin — <?= esc($brandName) ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/styles.css') ?>">
</head>
<body class="admin-body">
  <main class="admin-login-wrap">
    <a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">R.</span><span class="brand-name"><?= esc($brandName) ?></span></a>
    <section class="admin-panel admin-login-panel">
      <p class="eyebrow">PORTFOLIO CONTENT MANAGER</p>
      <h1>Masuk admin</h1>
      <p class="admin-muted">Kelola konten yang tersimpan di database portofolio.</p>
      <?php if ($error): ?><div class="admin-alert admin-alert-error" role="alert"><?= esc($error) ?></div><?php endif; ?>
      <?php if (! $configured): ?><p class="admin-setup-note">Atur <code>ADMIN_EMAIL</code> dan <code>ADMIN_PASSWORD_HASH</code> di file <code>.env</code> sebelum login. Petunjuk ada di README.</p><?php endif; ?>
      <form action="<?= site_url('admin/login') ?>" method="post" class="admin-form">
        <?= csrf_field() ?>
        <label class="admin-field"><span>Email</span><input name="email" type="email" autocomplete="username" required maxlength="190"></label>
        <label class="admin-field"><span>Password</span><input name="password" type="password" autocomplete="current-password" required></label>
        <button class="button button-primary" type="submit" <?= $configured ? '' : 'disabled' ?>>Masuk</button>
      </form>
      <a class="admin-back-link" href="<?= site_url('/') ?>">← Kembali ke portofolio</a>
    </section>
  </main>
</body>
</html>
