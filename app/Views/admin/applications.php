<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold">Application Management</h2>
            <p class="text-secondary mb-0">
                Review student scholarship applications.
            </p>
        </div>

        <a href="<?= site_url('admin/dashboard') ?>"
           class="btn btn-outline-secondary">
            Back to Dashboard
        </a>
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
                            <th>Student</th>
                            <th>Scholarship</th>
                            <th>Provider</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (! empty($applications)): ?>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?= esc($application['id']) ?></td>

                                <td>
                                    <?= esc(trim(
                                        ($application['first_name'] ?? '') . ' ' .
                                        ($application['middle_name'] ?? '') . ' ' .
                                        ($application['last_name'] ?? '')
                                    )) ?>
                                </td>

                                <td>
                                    <?= esc($application['scholarship_title']) ?>
                                </td>

                                <td>
                                    <?= esc($application['scholarship_provider']) ?>
                                </td>

                                <td>
                                    <?= esc($application['created_at']) ?>
                                </td>

                                <td>
                                    <?php
                                    $status = strtolower($application['status']);
                                    $badgeClass = match ($status) {
                                        'pending' => 'text-bg-warning',
                                        'approved' => 'text-bg-success',
                                        'rejected' => 'text-bg-danger',
                                        default => 'text-bg-secondary',
                                    };
                                    ?>

                                    <span class="badge <?= esc($badgeClass) ?>">
                                        <?= esc(ucfirst($application['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= esc($application['remarks'] ?? '—') ?>
                                </td>

                                <td>
                                    <div class="d-flex flex-column gap-2" style="min-width: 220px;">

                                        <a
                                            href="<?= site_url('admin/applications/view/' . $application['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View Details
                                        </a>

                                        <form
                                            action="<?= site_url('admin/applications/update-status/' . $application['id']) ?>"
                                            method="post"
                                            class="d-flex flex-column gap-2"
                                        >
                                            <?= csrf_field() ?>

                                            <select name="status" class="form-select form-select-sm" required>
                                                <option value="pending"
                                                    <?= $application['status'] === 'pending' ? 'selected' : '' ?>>
                                                    Pending
                                                </option>
                                                <option value="approved"
                                                    <?= $application['status'] === 'approved' ? 'selected' : '' ?>>
                                                    Approved
                                                </option>
                                                <option value="rejected"
                                                    <?= $application['status'] === 'rejected' ? 'selected' : '' ?>>
                                                    Rejected
                                                </option>
                                            </select>

                                            <textarea
                                                name="remarks"
                                                class="form-control form-control-sm"
                                                rows="2"
                                                maxlength="2000"
                                                placeholder="Optional remarks"
                                            ><?= esc($application['remarks'] ?? '') ?></textarea>

                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Update Status
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">
                                No applications found yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <p class="text-secondary small mb-0">
                Total applications: <?= count($applications) ?>
            </p>

        </div>
    </div>
</div>

</body>
</html>
