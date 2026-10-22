
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Management | Scholarship System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Student Management</h2>
            <p class="text-muted mb-0">
                View and search registered student accounts.
            </p>
        </div>

        <a href="<?= site_url('admin/dashboard') ?>"
           class="btn btn-outline-secondary">
            Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?= site_url('admin/students') ?>">
                <label for="q" class="form-label fw-semibold">
                    Search students
                </label>

                <div class="input-group">
                    <input
                        type="search"
                        id="q"
                        name="q"
                        class="form-control"
                        placeholder="Enter a name or email address"
                        value="<?= esc($search) ?>"
                        maxlength="100"
                    >

                    <button class="btn btn-primary" type="submit">
                        Search
                    </button>

                    <?php if ($search !== ''): ?>
                        <a href="<?= site_url('admin/students') ?>"
                           class="btn btn-outline-secondary">
                            Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">Registered Students</h5>
                <span class="badge text-bg-primary">
                    <?= count($students) ?> shown
                </span>
            </div>

            <small class="text-muted">
                <?= $search !== ''
                    ? 'Search results for: ' . esc($search)
                    : 'Showing up to 100 students, ordered by last name.' ?>
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Account Type</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <h6 class="text-muted">No students found</h6>
                                <p class="text-muted mb-0">
                                    Try another name or email address.
                                </p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <?php
                                $middleName = trim(
                                    (string) ($student['middle_name'] ?? '')
                                );

                                $fullName = trim(
                                    $student['first_name'] . ' ' .
                                    ($middleName !== '' ? $middleName . ' ' : '') .
                                    $student['last_name']
                                );
                            ?>
                            <tr>
                                <td><?= esc($student['id']) ?></td>

                                <td class="fw-semibold">
                                    <?= esc($fullName) ?>
                                </td>

                                <td><?= esc($student['email']) ?></td>

                                <td>
                                    <span class="badge text-bg-success">
                                        Student
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white text-muted small">
            Read-only listing. No student records can be edited or deleted here.
        </div>
    </div>

</div>

</body>
</html>
