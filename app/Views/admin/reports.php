<?php
$title = 'Reports';
$reportUrl = site_url('administrator/reports/view') . '?' . http_build_query($filters);
include __DIR__ . '/_header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="admin-page-eyebrow">Administrator</p>
        <h1 class="admin-page-title">Application reports</h1>
        <p class="admin-page-description">Choose report filters and generate a downloadable PDF for the matching applications.</p>
    </div>
</div>

<section class="admin-card p-4 mb-4">
    <h2 class="admin-section-title">Find application records</h2>
    <p class="text-muted small mb-4">Search existing applications, then view individual records or generate a PDF report from the matching results.</p>
    <form action="<?= site_url('administrator/reports') ?>" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-5">
                <label class="form-label" for="report-search">Search</label>
                <input class="form-control" id="report-search" type="search" name="q" value="<?= esc($filters['search']) ?>">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="report-status">Application status</label>
                <select class="form-select" id="report-status" name="status">
                    <option value="" <?= $filters['status'] === '' ? 'selected' : '' ?>>All statuses</option>
                    <?php foreach (['pending', 'under_review', 'incomplete', 'approved', 'rejected'] as $status): ?>
                        <option value="<?= esc($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $status))) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="report-scholarship">Scholarship</label>
                <select class="form-select" id="report-scholarship" name="scholarship_id">
                    <option value="" <?= $filters['scholarship_id'] === '' ? 'selected' : '' ?>>All scholarships</option>
                    <?php foreach ($scholarships as $scholarship): ?>
                        <option value="<?= (int) $scholarship['id'] ?>" <?= (string) $filters['scholarship_id'] === (string) $scholarship['id'] ? 'selected' : '' ?>><?= esc($scholarship['title']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-12 col-lg-1 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" type="submit">Search</button>
            </div>
            <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <a href="<?= site_url('administrator/reports') ?>">Clear filters</a>
                <a class="btn btn-outline-primary" href="<?= esc($reportUrl) ?>" target="_blank" rel="noopener">Generate PDF report</a>
            </div>
        </div>
    </form>
</section>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <div>
        <h2 class="admin-section-title mb-1">Application records</h2>
        <p class="admin-page-description">Existing applications matching the selected filters.</p>
    </div>
    <span class="badge text-bg-secondary"><?= count($applications) ?> record<?= count($applications) === 1 ? '' : 's' ?></span>
</div>

<?php if ($applications === []): ?>
    <div class="alert alert-info"><?= $filters['search'] !== '' || $filters['status'] !== '' || $filters['scholarship_id'] !== '' ? 'No applications match these filters.' : 'No applications have been submitted yet.' ?></div>
<?php else: ?>
    <div class="table-responsive bg-white rounded shadow-sm admin-table-wrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Applicant</th><th>Scholarship</th><th>Contact</th><th>Submitted</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><strong><?= esc($application['applicant_name'] ?: 'Applicant') ?></strong><div class="small text-muted">#<?= (int) $application['id'] ?></div></td>
                        <td><?= esc($application['scholarship_title']) ?></td>
                        <td><?= esc($application['email'] ?? '') ?><div class="small text-muted"><?= esc($application['mobile_number'] ?? '') ?></div></td>
                        <td><?= ! empty($application['created_at']) ? esc(date('M j, Y g:i A', strtotime($application['created_at']))) : '' ?></td>
                        <td><span class="badge text-bg-<?= $application['status'] === 'approved' ? 'success' : ($application['status'] === 'rejected' ? 'danger' : 'secondary') ?>"><?= esc(ucwords(str_replace('_', ' ', $application['status']))) ?></span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary admin-icon-action" href="<?= site_url('administrator/' . (int) $application['id']) ?>" aria-label="View application" title="View application">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 5c-5.2 0-9.4 4.1-11 7 1.6 2.9 5.8 7 11 7s9.4-4.1 11-7c-1.6-2.9-5.8-7-11-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                                    <span class="visually-hidden">View application</span>
                                </a>
                                <a class="btn btn-sm btn-outline-secondary admin-icon-action" href="<?= site_url('administrator/applications/' . (int) $application['id'] . '/print') ?>" target="_blank" rel="noopener" aria-label="Print application" title="Print application">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 8V3h12v5h1a3 3 0 0 1 3 3v7h-4v3H6v-3H2v-7a3 3 0 0 1 3-3h1Zm2-3v3h8V5H8Zm8 14v-5H8v5h8Zm2-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                                    <span class="visually-hidden">Print application</span>
                                </a>
                                <a class="btn btn-sm btn-outline-secondary admin-icon-action" href="<?= site_url('administrator/applications/' . (int) $application['id'] . '/edit') ?>" aria-label="Edit application" title="Edit application">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25Zm17.7-10.05a1 1 0 0 0 0-1.41l-2.49-2.49a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.98-1.68Z"/></svg>
                                    <span class="visually-hidden">Edit application</span>
                                </a>
                                <form action="<?= site_url('administrator/applications/' . (int) $application['id'] . '/delete') ?>" method="post" data-confirm="Permanently delete this application and its uploaded documents?" data-confirm-title="Delete application?" data-confirm-button="Delete application" data-confirm-variant="danger">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger admin-icon-action" type="submit" aria-label="Delete application" title="Delete application">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 7h12l-1 13H7L6 7Zm3-3h6l1 2H8l1-2ZM4 5h16v2H4V5Z"/></svg>
                                        <span class="visually-hidden">Delete application</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
<?php include __DIR__ . '/_footer.php'; ?>
