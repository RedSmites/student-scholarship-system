
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Student Registration</h1>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($validation) && $validation->getErrors()): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('register') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First name *</label>
                            <input class="form-control" id="first_name"
                                   name="first_name" maxlength="100" required
                                   autocomplete="given-name"
                                   value="<?= esc($old['first_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="middle_name" class="form-label">
                                Middle name (optional)
                            </label>
                            <input class="form-control" id="middle_name"
                                   name="middle_name" maxlength="100"
                                   autocomplete="additional-name"
                                   value="<?= esc($old['middle_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last name *</label>
                            <input class="form-control" id="last_name"
                                   name="last_name" maxlength="100" required
                                   autocomplete="family-name"
                                   value="<?= esc($old['last_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="suffix" class="form-label">
                                Suffix (optional)
                            </label>
                            <input class="form-control" id="suffix"
                                   name="suffix" maxlength="20"
                                   placeholder="Jr., Sr., III"
                                   value="<?= esc($old['suffix'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address *</label>
                            <input type="email" class="form-control" id="email"
                                   name="email" maxlength="190" required
                                   autocomplete="email"
                                   value="<?= esc($old['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password"
                                   name="password" minlength="12" maxlength="72"
                                   required autocomplete="new-password">
                            <div class="form-text">
                                Use at least 12 characters.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">
                                Confirm password *
                            </label>
                            <input type="password" class="form-control"
                                   id="password_confirm" name="password_confirm"
                                   minlength="12" maxlength="72" required
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Create account
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Already registered?
                        <a href="<?= site_url('login') ?>">Log in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
