<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm"><div class="card-body p-4">
                <h1 class="h3">Create an account</h1>
                <p class="text-muted">New accounts are created with the student role. To use this account for administration, promote it in the database before signing in.</p>

                <?php if (isset($validation) && $validation->getErrors()): ?>
                    <div class="alert alert-danger"><ul class="mb-0">
                        <?php foreach ($validation->getErrors() as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?>
                    </ul></div>
                <?php endif; ?>

                <?php $old = $old ?? []; ?>
                <form action="<?= site_url('register') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6"><label for="first_name" class="form-label">First name *</label><input class="form-control" id="first_name" name="first_name" value="<?= esc($old['first_name'] ?? '') ?>" maxlength="100" required></div>
                        <div class="col-md-6"><label for="last_name" class="form-label">Last name *</label><input class="form-control" id="last_name" name="last_name" value="<?= esc($old['last_name'] ?? '') ?>" maxlength="100" required></div>
                        <div class="col-md-6"><label for="middle_name" class="form-label">Middle name</label><input class="form-control" id="middle_name" name="middle_name" value="<?= esc($old['middle_name'] ?? '') ?>" maxlength="100"></div>
                        <div class="col-md-6"><label for="suffix" class="form-label">Suffix</label><input class="form-control" id="suffix" name="suffix" value="<?= esc($old['suffix'] ?? '') ?>" maxlength="20"></div>
                        <div class="col-12"><label for="email" class="form-label">Email address *</label><input class="form-control" type="email" id="email" name="email" value="<?= esc($old['email'] ?? '') ?>" maxlength="190" autocomplete="email" required></div>
                        <div class="col-md-6"><label for="password" class="form-label">Password *</label><input class="form-control" type="password" id="password" name="password" minlength="12" maxlength="72" autocomplete="new-password" required><div class="form-text">Use at least 12 characters.</div></div>
                        <div class="col-md-6"><label for="password_confirm" class="form-label">Confirm password *</label><input class="form-control" type="password" id="password_confirm" name="password_confirm" minlength="12" maxlength="72" autocomplete="new-password" required></div>
                    </div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Create account</button>
                </form>
                <p class="text-center mt-3 mb-0"><a href="<?= site_url('login') ?>">Administrator sign in</a></p>
            </div></div>
        </div>
    </div>
</main>
</body>
</html>
