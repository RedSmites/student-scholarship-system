<?php $title = 'Administrator dashboard'; include APPPATH . 'Views/admin/_header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5">
    <div>
        <p class="admin-page-eyebrow">Scholarship administration</p>
        <h1 class="admin-page-title">Welcome, <?= esc($name ?? 'Administrator') ?></h1>
        <p class="admin-page-description">A current overview of applications and open scholarship programs.</p>
    </div>
    <a class="btn btn-primary admin-review-cta" href="<?= site_url('administrator/applications') ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8 2h8a2 2 0 0 1 2 2h2a2 2 0 0 1 2 2v9.1a6.5 6.5 0 0 0-2-.8V6h-2v2H6V6H4v14h8.1c.2.7.5 1.4.9 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2-2Zm1 2v2h6V4H9Zm-2 7h6v2H7v-2Zm0 4h4v2H7v-2Zm11 0a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm3.5 6.1 2.5 2.5-1.4 1.4-2.5-2.5a6 6 0 1 1 1.4-1.4Z"/></svg><span>Review applications</span></a>
</div>

<div class="row g-3 mt-4 mb-5">
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Total applications</p>
            <h3><?= (int) $totalApplications ?></h3>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Applications to review</p>
            <h3><?= (int) $pendingApplications ?></h3>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-safe h-100">
            <p class="text-muted mb-1">Approved applications</p>
            <h3><?= (int) $approvedApplications ?></h3>
        </section>
    </div>
    <div class="col-sm-6 col-xl-3">
        <section class="admin-card admin-stat-card is-total h-100">
            <p class="text-muted mb-1">Open scholarships</p>
            <h3><?= (int) $activeScholarships ?></h3>
        </section>
    </div>
</div>

<section class="admin-card mt-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="admin-section-title">Recent applications</h2>
            <p class="small text-muted mb-0">The latest submissions from the student application portal.</p>
        </div>
        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('administrator/applications') ?>">View all applications</a>
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
                            <td><a class="btn btn-sm btn-outline-primary admin-icon-action" href="<?= site_url('administrator/' . (int) $application['id']) ?>" data-tooltip="Review application" aria-label="Review application" title="Review application"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8 2h8a2 2 0 0 1 2 2h2a2 2 0 0 1 2 2v9.1a6.5 6.5 0 0 0-2-.8V6h-2v2H6V6H4v14h8.1c.2.7.5 1.4.9 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2-2Zm1 2v2h6V4H9Zm-2 7h6v2H7v-2Zm0 4h4v2H7v-2Zm11 0a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm3.5 6.1 2.5 2.5-1.4 1.4-2.5-2.5a6 6 0 1 1 1.4-1.4Z"/></svg><span class="visually-hidden">Review</span></a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
<?php include APPPATH . 'Views/admin/_footer.php'; ?>
