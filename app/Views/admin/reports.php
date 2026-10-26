<?php
$title = 'Reports';
$applicantPreview = site_url('administrator/reports/pdf');
$scholarshipPreview = site_url('administrator/reports/scholarships/pdf');
include __DIR__ . '/_header.php';
?>
<div class="mb-4">
    <p class="admin-page-eyebrow">Administrator</p>
    <h1 class="admin-page-title">Reports</h1>
    <p class="admin-page-description">Preview or download consolidated reports for applications and scholarship programs.</p>
</div>
<div class="admin-report-grid">
    <article class="admin-card admin-report-card">
        <div><p class="admin-page-eyebrow">Applicant records</p><h2>Application report</h2><p>All submitted applications, their scholarship, contact details, submission date, and review status.</p><strong><?= number_format($applicationCount) ?> applications</strong></div>
        <div class="admin-report-actions"><a class="btn btn-primary" href="<?= esc($applicantPreview) ?>" target="_blank" rel="noopener">Preview report</a><a class="btn btn-outline-secondary" href="<?= esc($applicantPreview . '?download=1') ?>">Download PDF</a></div>
    </article>
    <article class="admin-card admin-report-card">
        <div><p class="admin-page-eyebrow">Program records</p><h2>Scholarship programs report</h2><p>A consolidated list of scholarship programs, providers, and current availability.</p><strong><?= number_format(count($scholarships)) ?> programs</strong></div>
        <div class="admin-report-actions"><a class="btn btn-primary" href="<?= esc($scholarshipPreview) ?>" target="_blank" rel="noopener">Preview report</a><a class="btn btn-outline-secondary" href="<?= esc($scholarshipPreview . '?download=1') ?>">Download PDF</a></div>
    </article>
</div>
<?php include __DIR__ . '/_footer.php'; ?>
