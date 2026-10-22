<!doctype html>
<html lang="en">
<head>
<<<<<<< HEAD
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administrator registration | Municipal Scholarship</title>
  <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/css/student.css') ?>" rel="stylesheet">
=======
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
>>>>>>> 06a59cbe467985b9066497ce222b50e461966c1b
</head>
<body class="admin-auth-page">
<?php $old = $old ?? []; $validation = $validation ?? null; $errorFor = static fn (string $field): string => $validation ? (string) $validation->getError($field) : ''; ?>
<main class="admin-auth-wrap">
  <section class="admin-auth-card admin-auth-register" aria-labelledby="admin-register-title">
    <div class="admin-auth-mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2 4.5 5v6.1c0 5 3.2 9.5 7.5 10.9 4.3-1.4 7.5-5.9 7.5-10.9V5L12 2Zm0 3.1 4.5 1.8v4.2c0 3.5-2 6.8-4.5 8-2.5-1.2-4.5-4.5-4.5-8V6.9L12 5.1Z"/></svg></div>
    <p class="admin-auth-eyebrow">MUNICIPAL SCHOLARSHIP PROGRAM</p>
    <h1 id="admin-register-title">Create administrator account</h1>
    <p class="text-muted">Enter your details to create an administrator account.</p>
    <form action="<?= site_url('administrator/register') ?>" method="post">
      <?= csrf_field() ?>
      <div class="row g-3">
        <?php foreach (['first_name'=>'First name','last_name'=>'Last name','middle_name'=>'Middle name','suffix'=>'Suffix'] as $field=>$label): $fieldError=$errorFor($field); $required=in_array($field,['first_name','last_name'],true); ?>
        <div class="col-md-6"><label for="<?= $field ?>" class="form-label"><?= $label ?><?= $required?' *':'' ?></label><input class="form-control <?= $fieldError?'is-invalid':'' ?>" id="<?= $field ?>" name="<?= $field ?>" value="<?= esc($old[$field]??'') ?>" maxlength="<?= $field==='suffix'?'20':'100' ?>" <?= $required?'required':'' ?>><?php if($fieldError): ?><div class="invalid-feedback"><?= esc($fieldError) ?></div><?php endif ?></div>
        <?php endforeach ?>
        <?php $fieldError=$errorFor('email'); ?>
        <div class="col-12"><label for="email" class="form-label">Email address *</label><input class="form-control <?= $fieldError?'is-invalid':'' ?>" type="email" id="email" name="email" value="<?= esc($old['email']??'') ?>" maxlength="190" autocomplete="email" required><?php if($fieldError): ?><div class="invalid-feedback"><?= esc($fieldError) ?></div><?php endif ?></div>
        <?php $fieldError=$errorFor('password'); ?>
        <div class="col-md-6"><label for="password" class="form-label">Password *</label><input class="form-control <?= $fieldError?'is-invalid':'' ?>" type="password" id="password" name="password" minlength="12" maxlength="72" autocomplete="new-password" required><div class="form-text">Use at least 12 characters.</div><?php if($fieldError): ?><div class="invalid-feedback d-block"><?= esc($fieldError) ?></div><?php endif ?></div>
        <?php $fieldError=$errorFor('password_confirm'); ?>
        <div class="col-md-6"><label for="password_confirm" class="form-label">Confirm password *</label><input class="form-control <?= $fieldError?'is-invalid':'' ?>" type="password" id="password_confirm" name="password_confirm" minlength="12" maxlength="72" autocomplete="new-password" required><?php if($fieldError): ?><div class="invalid-feedback"><?= esc($fieldError) ?></div><?php endif ?></div>
      </div>
      <button class="btn btn-primary w-100 mt-4" type="submit">Create administrator account</button>
    </form>
    <p class="admin-auth-footnote">Already registered? <a href="<?= site_url('administrator') ?>">Administrator sign in</a></p>
    <p class="text-center mb-0"><a class="admin-auth-back" href="<?= site_url('/') ?>">? Back to the scholarship portal</a></p>
  </section>
</main>
</body></html>
