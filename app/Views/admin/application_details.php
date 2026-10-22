
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<?php
$status = strtolower((string) $application['status']);

$badgeClass = match ($status) {
    'pending' => 'text-bg-warning',
    'approved' => 'text-bg-success',
    'rejected' => 'text-bg-danger',
    default => 'text-bg-secondary',
};

$studentName = trim(
    ($application['first_name'] ?? '') . ' ' .
    ($application['middle_name'] ?? '') . ' ' .
    ($application['last_name'] ?? '')
);
?>

<div class="container py-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Application Details</h2>
            <p class="text-secondary mb-0">
                Review the student's application and scholarship information.
            </p>
        </div>

        <a href="<?= site_url('admin/applications') ?>"
           class="btn btn-outline-secondary">
            Back to Applications
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">

            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <p class="text-secondary mb-1">Application ID</p>
                    <h4 class="fw-bold">#<?= esc($application['id']) ?></h4>
                </div>

                <div>
                    <p class="text-secondary mb-1">Current Status</p>
                    <span class="badge <?= esc($badgeClass) ?> fs-6">
                        <?= esc(ucfirst($application['status'])) ?>
                    </span>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold mb-3">Student Information</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <p class="text-secondary mb-1">Full Name</p>
                    <p class="fw-semibold"><?= esc($studentName) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Email Address</p>
                    <p class="fw-semibold"><?= esc($application['email']) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Student ID</p>
                    <p class="fw-semibold"><?= esc($application['user_id']) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Date Applied</p>
                    <p class="fw-semibold"><?= esc($application['created_at'] ?? 'Not available') ?></p>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold mb-3">Scholarship Information</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <p class="text-secondary mb-1">Scholarship Title</p>
                    <p class="fw-semibold"><?= esc($application['scholarship_title']) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Provider</p>
                    <p class="fw-semibold"><?= esc($application['scholarship_provider']) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Deadline</p>
                    <p class="fw-semibold">
                        <?= esc($application['scholarship_deadline'] ?: 'Not specified') ?>
                    </p>
                </div>

                <div class="col-12">
                    <p class="text-secondary mb-1">Description</p>
                    <p><?= nl2br(esc($application['scholarship_description'])) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Eligibility</p>
                    <p><?= nl2br(esc($application['scholarship_eligibility'])) ?></p>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Benefits</p>
                    <p><?= nl2br(esc($application['scholarship_benefits'])) ?></p>
                </div>
            </div>

            <hr>

            <h5 class="fw-bold mb-3">Application Decision</h5>

            <div class="mb-3">
                <p class="text-secondary mb-1">Remarks</p>
                <p><?= nl2br(esc($application['remarks'] ?: 'No remarks provided.')) ?></p>
            </div>

            <div>
                <p class="text-secondary mb-1">Last Updated</p>
                <p class="fw-semibold">
                    <?= esc($application['updated_at'] ?? 'Not available') ?>
                </p>
            </div>

        </div>
    </div>

</div>

</body>
</html>
