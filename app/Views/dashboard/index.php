<?php $title = 'Administrator dashboard'; include APPPATH . 'Views/admin/_header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="text-uppercase small text-primary fw-bold mb-1">Scholarship administration</p>
        <h1 class="h2 mb-1">Welcome, <?= esc($name ?? 'Administrator') ?></h1>
        <p class="text-muted mb-0">A current overview of applications and open scholarship programs.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('administrator') ?>">Review applications</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Total applications</p>
            <h3><?= (int) $totalApplications ?></h3>
            <p class="small mb-0">All submitted applications</p>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Applications to review</p>
            <h3><?= (int) $pendingApplications ?></h3>
            <p class="small mb-0">Pending, under review, or incomplete</p>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-safe h-100">
            <p class="text-muted mb-1">Approved applications</p>
            <h3><?= (int) $approvedApplications ?></h3>
            <p class="small mb-0">Applications with an approved decision</p>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Open scholarships</p>
            <h3><?= (int) $activeScholarships ?></h3>
            <p class="small mb-0">Currently accepting applications</p>
        </section>
    </div>
</div>

<section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="h5 mb-1">Recent applications</h2>
            <p class="small text-muted mb-0">The latest submissions from the student application portal.</p>
        </div>
        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('administrator') ?>">View all applications</a>
    </div>

    <?php if (empty($recentApplications)): ?>
        <p class="text-muted mb-0">No applications have been submitted yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Applicant</th>
                        <th scope="col">Scholarship</th>
                        <th scope="col">Submitted</th>
                        <th scope="col">Status</th>
                        <th scope="col"><span class="visually-hidden">Action</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentApplications as $application): ?>
                        <?php
                        $statusClass = match ($application['status']) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'incomplete' => 'warning',
                            default => 'secondary',
                        };
                        ?>
                        <tr>
                            <td>
                                <strong><?= esc($application['applicant_name'] ?: 'Applicant') ?></strong>
                                <div class="small text-muted"><?= esc($application['email'] ?? '') ?></div>
                            </td>
                            <td><?= esc($application['scholarship_title'] ?? 'Scholarship unavailable') ?></td>
                            <td><?= esc($application['created_at'] ?? '') ?></td>
                            <td><span class="badge text-bg-<?= $statusClass ?>"><?= esc(ucwords(str_replace('_', ' ', $application['status']))) ?></span></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="<?= site_url('administrator/' . (int) $application['id']) ?>">Review</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
<?php include APPPATH . 'Views/admin/_footer.php'; ?>
