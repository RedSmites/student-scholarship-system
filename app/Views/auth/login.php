<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administrator Login | Municipal Scholarship</title>
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
</head>
<body class="admin-auth-page">
<main class="admin-auth-wrap">
  <section class="admin-auth-card" aria-labelledby="admin-login-title">
    <h1 id="admin-login-title">Administrator<br>Login</h1>
    <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success py-2 small"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
    <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger py-2 small"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
    <?php if (isset($validation) && $validation->getErrors()): ?><div class="alert alert-danger py-2 small" role="alert"><ul class="mb-0 ps-3"><?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul></div><?php endif ?>
    <form action="<?= site_url('administrator') ?>" method="post">
      <?= csrf_field() ?>
      <div class="mb-3"><label for="email" class="form-label">Email address</label><input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email') ?? '') ?>" autocomplete="username" maxlength="190" required></div>
      <div class="mb-4"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required></div>
      <button type="submit" class="btn admin-auth-submit w-100">Sign in</button>
    </form>
    <a class="admin-auth-back" href="<?= site_url('/') ?>">&larr; Back to main site</a>
  </section>
</main>
</body></html>
