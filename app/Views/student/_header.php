<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Municipal Scholarship Program') ?></title>
  <meta name="description" content="Municipal Scholarship Program student application portal">
  <link href="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" rel="icon" type="image/png">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
</head>
<body>
<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">
    <button type="button" class="mobile-nav-toggle d-xl-none" aria-label="Open navigation" aria-expanded="false"><i class="bi bi-list" aria-hidden="true"></i></button>
    <a href="<?= site_url('/') ?>" class="logo d-flex align-items-center me-auto">
      <img src="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" alt="Santa Ignacia municipal seal">
      <h1 class="sitename">Santa Ignacia LGU</h1>
    </a>
    <nav id="navmenu" class="navmenu" aria-label="Main navigation"><ul>
      <li><a href="<?= site_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= site_url('requirements') ?>" class="<?= url_is('requirements') ? 'active' : '' ?>">Requirements</a></li>
    </ul></nav>
    <a class="btn-cta" href="<?= site_url('apply') ?>">Apply Now</a>
  </div>
</header>
<main class="main">
