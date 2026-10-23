<?php
$title = $title ?? 'Scholarship administration';
$currentUri = trim(uri_string(), '/');
$applicationsActive = $currentUri === 'administrator/applications' || preg_match('~^administrator/(?:\d+|document/\d+)~', $currentUri) === 1;
$scholarshipsActive = str_starts_with($currentUri, 'scholarships');
$settingsActive = $currentUri === 'administrator/settings';
$adminName = (string) (session()->get('user_name') ?: 'Administrator');
$adminInitial = strtoupper(substr($adminName, 0, 1));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title) ?> | Municipal Scholarship</title>
  <link href="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" rel="icon" type="image/png">
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/admin-review.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/admin-review.css') ?>" rel="stylesheet">
</head>
<body class="admin-shell-page">
<div class="admin-shell">
  <aside class="admin-sidebar" id="admin-sidebar" aria-label="Administrator navigation">
    <a class="admin-sidebar-brand" href="<?= site_url('dashboard') ?>">
      <span class="admin-brand-mark" aria-hidden="true">MS</span>
      <span class="admin-brand-copy"><strong>Municipal</strong><small>Scholarship System</small></span>
    </a>
    <nav class="admin-side-nav">
      <p class="admin-nav-caption">MAIN</p>
      <a class="admin-nav-link <?= url_is('dashboard') ? 'is-active' : '' ?>" href="<?= site_url('dashboard') ?>" title="Dashboard">
        <i class="ti ti-home" aria-hidden="true"></i><span>Dashboard</span>
      </a>
      <p class="admin-nav-caption">APPLICATIONS</p>
      <a class="admin-nav-link <?= $applicationsActive ? 'is-active' : '' ?>" href="<?= site_url('administrator/applications') ?>" title="Applications">
        <i class="ti ti-clipboard-list" aria-hidden="true"></i><span>Applications</span>
      </a>
      <p class="admin-nav-caption">PROGRAMS</p>
      <a class="admin-nav-link <?= $scholarshipsActive ? 'is-active' : '' ?>" href="<?= site_url('scholarships') ?>" title="Scholarship programs">
        <i class="ti ti-school" aria-hidden="true"></i><span>Scholarship programs</span>
      </a>
      <p class="admin-nav-caption">ACCOUNT</p>
      <a class="admin-nav-link <?= $settingsActive ? 'is-active' : '' ?>" href="<?= site_url('administrator/settings') ?>" title="Settings">
        <i class="ti ti-settings" aria-hidden="true"></i><span>Settings</span>
      </a>
    </nav>
    <div class="admin-sidebar-bottom">

      <form action="<?= site_url('logout') ?>" method="post" class="admin-logout-form" data-confirm="Are you sure you want to log out?" data-confirm-title="Log out?" data-confirm-button="Yes, log out" data-confirm-variant="danger">
        <?= csrf_field() ?>
        <button class="admin-nav-link admin-logout-link" type="submit" title="Log out">
          <i class="ti ti-logout" aria-hidden="true"></i><span>Log out</span>
        </button>
      </form>
    </div>
  </aside>
  <button class="admin-sidebar-backdrop" id="admin-sidebar-backdrop" type="button" aria-label="Close navigation"></button>
  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true" aria-controls="admin-sidebar">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <div class="admin-topbar-title"><span><?= esc($title) ?></span><small>Municipal Scholarship System</small></div>
      <details class="admin-profile-menu">
        <summary class="admin-topbar-user" aria-label="Open profile menu"><span class="admin-user-avatar"><?= esc($adminInitial) ?></span><span class="admin-user-copy"><strong><?= esc($adminName) ?></strong><small>Administrator</small></span><svg class="admin-profile-chevron" viewBox="0 0 16 16" aria-hidden="true"><path d="m3 6 5 5 5-5"/></svg></summary>
        <div class="admin-profile-dropdown"><div class="admin-profile-heading"><strong><?= esc($adminName) ?></strong><small><?= esc(session()->get('user_email') ?? '') ?></small></div><a href="<?= site_url('administrator/settings') ?>">Profile &amp; settings</a><form action="<?= site_url('logout') ?>" method="post" data-confirm="Are you sure you want to log out?" data-confirm-title="Log out?" data-confirm-button="Yes, log out" data-confirm-variant="danger"><?= csrf_field() ?><button type="submit">Log out</button></form></div>
      </details>
    </header>
    <main class="admin-review-shell">
      <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
      <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
