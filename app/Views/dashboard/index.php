<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('dashboard') ?>">
            Scholarship System
        </a>

        <form action="<?= site_url('logout') ?>" method="post" class="mb-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-light btn-sm">
                Logout
            </button>
        </form>
    </div>
</nav>

<main class="container py-5">
    <div class="mb-4">
        <h1 class="h2">
            Welcome, <?= esc($name ?? 'Student') ?>!
        </h1>
        <p class="text-muted">
            Welcome to your Student Scholarship Information System.
        </p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Browse Scholarships</h2>
                    <p class="text-muted">
                        Explore available scholarship opportunities.
                    </p>
                    <span class="badge text-bg-secondary">
                        <a href="<?= site_url('scholarships') ?>"
                            class="btn btn-primary">
                                Browse Scholarships
                        </a>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">My Applications</h2>
                    <p class="text-muted">
                        Track the status of your scholarship applications.
                    </p>
                    <span class="badge text-bg-secondary">
                        Coming soon
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">My Account</h2>
                    <p class="mb-2">
                        <strong>Name:</strong>
                        <?= esc($name ?? '') ?>
                    </p>
                    <p class="mb-2">
                        <strong>Email:</strong>
                        <?= esc($email ?? '') ?>
                    </p>
                    <p class="mb-0">
                        <strong>Role:</strong>
                        <?= esc(ucfirst($role ?? 'student')) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Your account is logged in successfully. More scholarship features
        will be added next.
    </div>
</main>

</body>
</html>
