<!doctype html>
<html lang="en"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Scholarship administration') ?> | Municipal Scholarship</title>
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/admin-review.css') ?>" rel="stylesheet">
</head><body>
<header class="admin-topbar"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
  <a class="brand" href="<?= site_url('dashboard') ?>">Municipal Scholarship Admin</a>
  <div class="d-flex flex-wrap align-items-center gap-3">
    <nav class="d-flex flex-wrap gap-3" aria-label="Administrator navigation">
      <a href="<?= site_url('dashboard') ?>" <?= url_is('dashboard') ? 'aria-current="page"' : '' ?>>Dashboard</a>
      <a href="<?= site_url('administrator') ?>" <?= url_is('administrator*') ? 'aria-current="page"' : '' ?>>Applications</a>
      <a href="<?= site_url('scholarships') ?>" <?= url_is('scholarships') ? 'aria-current="page"' : '' ?>>Scholarships</a>
    </nav>
    <a href="<?= site_url('/') ?>">Public portal</a>
    <form action="<?= site_url('logout') ?>" method="post" class="mb-0"><?= csrf_field() ?><button class="btn btn-sm btn-outline-light" type="submit">Log out</button></form>
  </div>
</div></header>
<main class="container py-4 admin-review-shell">
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
