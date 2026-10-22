<?php
$title = 'Account settings';
include APPPATH . 'Views/admin/_header.php';
$old = $old ?? [];
$validation = $validation ?? null;
$fieldError = static fn (string $field): string => $validation ? (string) $validation->getError($field) : '';
$value = static fn (string $field): string => esc($old[$field] ?? $user[$field] ?? '');
?>
<div class="admin-settings-heading">
  <div><p class="admin-settings-eyebrow">ACCOUNT</p><h1>Profile &amp; settings</h1><p>Update your administrator details and password.</p></div>
</div>
<form action="<?= site_url('administrator/settings') ?>" method="post" class="admin-settings-form">
  <?= csrf_field() ?>
  <section class="admin-settings-card">
    <div class="admin-settings-card-heading"><span class="admin-settings-avatar"><?= esc(strtoupper(substr($user['first_name'] ?? 'A', 0, 1))) ?></span><div><h2>Profile information</h2><p>Manage the name and email address associated with your account.</p></div></div>
    <div class="row g-3">
      <?php foreach (['first_name'=>'First name','middle_name'=>'Middle name','last_name'=>'Last name','suffix'=>'Suffix'] as $field=>$label): $error=$fieldError($field); $required=in_array($field,['first_name','last_name'],true); ?>
      <div class="col-md-6"><label class="form-label" for="<?= $field ?>"><?= $label ?><?= $required?' *':'' ?></label><input class="form-control <?= $error?'is-invalid':'' ?>" id="<?= $field ?>" name="<?= $field ?>" maxlength="<?= $field==='suffix'?'20':'100' ?>" value="<?= $value($field) ?>" <?= $required?'required':'' ?>><?php if($error): ?><div class="invalid-feedback"><?= esc($error) ?></div><?php endif ?></div>
      <?php endforeach; $error=$fieldError('email'); ?>
      <div class="col-12"><label class="form-label" for="email">Email address *</label><input class="form-control <?= $error?'is-invalid':'' ?>" type="email" id="email" name="email" maxlength="190" autocomplete="email" value="<?= $value('email') ?>" required><?php if($error): ?><div class="invalid-feedback"><?= esc($error) ?></div><?php endif ?></div>
    </div>
  </section>
  <section class="admin-settings-card">
    <div class="admin-settings-card-heading"><span class="admin-settings-password-mark" aria-hidden="true">?</span><div><h2>Change password</h2><p>Leave these fields blank to keep your current password. Use at least 12 characters.</p></div></div>
    <div class="row g-3">
      <?php foreach (['current_password'=>'Current password','new_password'=>'New password','confirm_new_password'=>'Confirm new password'] as $field=>$label): $error=$fieldError($field); ?>
      <div class="col-md-6"><label class="form-label" for="<?= $field ?>"><?= $label ?><?= $field==='current_password'?' (required when changing password)':'' ?></label><input class="form-control <?= $error?'is-invalid':'' ?>" type="password" id="<?= $field ?>" name="<?= $field ?>" autocomplete="<?= $field==='current_password'?'current-password':'new-password' ?>" <?= $field==='current_password'?'':'minlength="12" maxlength="72"' ?>><?php if($error): ?><div class="invalid-feedback"><?= esc($error) ?></div><?php endif ?></div>
      <?php endforeach ?>
    </div>
  </section>
  <div class="admin-settings-actions"><button type="submit" class="btn btn-primary">Save settings</button></div>
</form>
<?php include APPPATH . 'Views/admin/_footer.php'; ?>
