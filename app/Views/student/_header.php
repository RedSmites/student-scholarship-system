<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Municipal Scholarship Program') ?></title>
  <meta name="description" content="Municipal Scholarship Program student application portal">
  <link href="<?= base_url('assets/img/favicon.png') ?>" rel="icon">
  <link href="<?= base_url('assets/css/fonts.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
</head>
<body>
<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">
    <button type="button" class="mobile-nav-toggle d-xl-none" aria-label="Open navigation" aria-expanded="false">☰</button>
    <a href="<?= site_url('/') ?>" class="logo d-flex align-items-center me-auto">
      <img src="<?= base_url('assets/img/municipal-seal-placeholder.svg') ?>" alt="Municipal logo placeholder">
      <h1 class="sitename">Municipal Scholarship</h1>
    </a>
    <nav id="navmenu" class="navmenu" aria-label="Main navigation"><ul>
      <li><a href="<?= site_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= site_url('requirements') ?>" class="<?= url_is('requirements') ? 'active' : '' ?>">Requirements</a></li>
    </ul></nav>
    <a class="btn-cta" href="<?= site_url('apply') ?>">Apply Now</a>
  </div>
</header>
<main class="main">
