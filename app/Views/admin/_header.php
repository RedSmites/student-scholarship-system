<!doctype html>
<html lang="en"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Application Review') ?> Â· Scholarship Admin</title>
  <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
</head><body>
<header class="admin-topbar"><div class="container d-flex justify-content-between align-items-center">
  <a class="brand" href="<?= site_url('administrator') ?>">Scholarship application review</a>
  <div class="d-flex align-items-center gap-3"><a href="<?= site_url('/') ?>">Public portal</a><form action="<?= site_url('logout') ?>" method="post" class="mb-0"><?= csrf_field() ?><button class="btn btn-sm btn-outline-light" type="submit">Log out</button></form></div>
</div></header>
<main class="container py-4 admin-review-shell">
<?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>

