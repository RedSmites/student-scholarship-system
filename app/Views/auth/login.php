
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrator Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-2">Administrator Sign In</h1>
                    <p class="text-muted mb-4">Applicants do not need an account to apply.</p>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($validation) && $validation->getErrors()): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($validation->getErrors() as $error): ?>
                                <p class="mb-1"><?= esc($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control"
                                   id="email" name="email"
                                   autocomplete="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control"
                                   id="password" name="password"
                                   autocomplete="current-password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Log in
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0"><a href="<?= site_url('register') ?>">Create an account</a></p>
                    <p class="text-center mt-2 mb-0"><a href="<?= site_url('/') ?>">Return to the scholarship portal</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
