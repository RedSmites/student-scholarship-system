```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Scholarship System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f7fb;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: #172554;
            color: white;
        }

        .sidebar a {
            display: block;
            color: #dbeafe;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
        }

        .dashboard-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #172554;
        }

        .stat-label {
            color: #64748b;
        }

        .content {
            padding: 30px;
        }

        .section-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        section[id] {
            scroll-margin-top: 20px;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .content {
                padding: 20px 14px;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 sidebar p-4">
            <div class="brand mb-4">
                🎓 Scholarship System
            </div>

            <nav aria-label="Admin navigation">
                <a href="<?= site_url('admin/dashboard') ?>" class="active">
                    🏠 Dashboard
                </a>

                <a href="<?= site_url('admin/dashboard') ?>#overview">
                    📊 Overview
                </a>

                <a href="<?= site_url('admin/scholarships') ?>">
                    🎓 Scholarships
                </a>

                <a href="<?= site_url('admin/applications') ?>">
                    📄 Applications
                </a>

                <a href="<?= site_url('admin/students') ?>">
                    👥 Students
                </a>

                <hr class="border-light">

                <a href="<?= site_url('/') ?>">
                    🌐 Visit Website
                </a>

                <form action="<?= site_url('logout') ?>" method="post" class="mt-3">
                    <?= csrf_field() ?>

                    <button type="submit"
                            class="btn btn-outline-light w-100 text-start">
                        🚪 Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 content">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3 mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Admin Dashboard</h2>
                    <p class="text-secondary mb-0">
                        Welcome back,
                        <?= esc(session()->get('user_name') ?? 'Administrator') ?>!
                    </p>
                </div>

                <span class="badge text-bg-primary p-2">
                    Administrator
                </span>
            </div>

            <!-- Summary Cards -->
            <section id="overview" class="mb-4">
                <h5 class="fw-bold mb-3">System Overview</h5>

                <div class="row g-3">

                    <div class="col-sm-6 col-xl-3">
                        <div class="card dashboard-card">
                            <div class="card-body p-4">
                                <div class="stat-label">Total Students</div>
                                <div class="stat-number">
                                    <?= esc($totalStudents) ?>
                                </div>
                                <div class="small text-secondary">
                                    Registered student accounts
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card dashboard-card">
                            <div class="card-body p-4">
                                <div class="stat-label">Scholarships</div>
                                <div class="stat-number">
                                    <?= esc($totalScholarships) ?>
                                </div>
                                <div class="small text-secondary">
                                    Total scholarship records
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card dashboard-card">
                            <div class="card-body p-4">
                                <div class="stat-label">Open Scholarships</div>
                                <div class="stat-number">
                                    <?= esc($openScholarships) ?>
                                </div>
                                <div class="small text-secondary">
                                    Currently marked as open
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="card dashboard-card">
                            <div class="card-body p-4">
                                <div class="stat-label">Applications</div>
                                <div class="stat-number">
                                    <?= esc($totalApplications) ?>
                                </div>
                                <div class="small text-secondary">
                                    Total submitted applications
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Application Monitoring -->
            <section id="applications" class="mb-4">
                <div class="card section-card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between
                                    align-items-center gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    Application Monitoring
                                </h5>
                                <p class="text-secondary mb-0">
                                    Applications awaiting review
                                </p>
                            </div>

                            <div class="text-warning fs-2 fw-bold">
                                <?= esc($pendingApplications) ?>
                            </div>
                        </div>

                        <a href="<?= site_url('admin/applications') ?>"
                           class="btn btn-outline-primary btn-sm mt-3">
                            Review Applications
                        </a>
                    </div>
                </div>
            </section>

            <!-- Quick Access -->
            <section class="mb-4">
                <h5 class="fw-bold mb-3">Quick Access</h5>

                <div class="row g-3">

                    <!-- Scholarships -->
                    <div class="col-md-4">
                        <div class="card section-card h-100">
                            <div class="card-body p-4">
                                <h5>🎓 Scholarships</h5>

                                <p class="text-secondary">
                                    Create, update, and manage scholarship listings.
                                </p>

                                <a href="<?= site_url('admin/scholarships') ?>"
                                   class="btn btn-primary">
                                    Manage Scholarships
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Applications -->
                    <div class="col-md-4">
                        <div class="card section-card h-100">
                            <div class="card-body p-4">
                                <h5>📄 Applications</h5>

                                <p class="text-secondary">
                                    Review applications and update their status.
                                </p>

                                <a href="<?= site_url('admin/applications') ?>"
                                   class="btn btn-outline-primary">
                                    Manage Applications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Students -->
                    <div class="col-md-4" id="users">
                        <div class="card section-card h-100">
                            <div class="card-body p-4">
                                <h5>👥 Students</h5>

                                <p class="text-secondary">
                                    View the total number of registered student accounts.
                                </p>

                                <a href="<?= site_url('admin/students') ?>"class="btn btn-outline-primary">
                                    Manage Students
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <footer class="text-center text-secondary small mt-5">
                Student Scholarship Information System
            </footer>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>