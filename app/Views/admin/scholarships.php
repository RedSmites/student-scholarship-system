<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold">Scholarship Management</h2>
            <p class="text-secondary mb-0">
                View and manage scholarship records.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/dashboard') ?>"
            class="btn btn-outline-secondary">
                Back to Dashboard
            </a>

            <a href="<?= site_url('admin/scholarships/create') ?>"
            class="btn btn-primary">
                + Add Scholarship
            </a>
        </div>
    </div>


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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Scholarship</th>
                            <th>Provider</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (! empty($scholarships)): ?>
                        <?php foreach ($scholarships as $scholarship): ?>
                            <tr>
                                <td><?= esc($scholarship['id']) ?></td>
                                <td>
                                    <strong>
                                        <?= esc($scholarship['title']) ?>
                                    </strong>
                                </td>
                                <td><?= esc($scholarship['provider']) ?></td>
                                <td>
                                    <?= esc(
                                        $scholarship['deadline'] ?: 'Not specified'
                                    ) ?>
                                </td>
                                <td>
                                    <?php if ($scholarship['status'] === 'open'): ?>
                                        <span class="badge text-bg-success">
                                            Open
                                        </span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">
                                            <?= esc(ucfirst($scholarship['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= site_url('admin/scholarships/edit/' . $scholarship['id']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form action="<?= site_url('admin/scholarships/delete/' . $scholarship['id']) ?>"
                                            method="post"
                                            onsubmit="return confirm('Are you sure you want to delete this scholarship?');">

                                            <?= csrf_field() ?>

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No scholarships found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="text-secondary small mb-0">
                Total records displayed: <?= count($scholarships) ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>
