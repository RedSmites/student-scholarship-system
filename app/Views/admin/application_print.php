<?php
$details = $application['details'];
$skipDetails = ['csrf_test_name', 'declaration', 'email', 'mobile_number', 'scholarship_id'];
$labels = [
    'date_of_birth' => 'Date of birth',
    'place_of_birth' => 'Place of birth',
    'barangay' => 'Barangay',
    'street' => 'Street',
    'school_name' => 'Current school',
    'school_type' => 'School type',
    'academic_level' => 'Academic level',
    'parents_income' => 'Parents’ monthly income',
    'course' => 'Course',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application #<?= (int) $application['id'] ?> | Municipal Scholarship</title>
    <link href="<?= base_url('assets/css/fonts.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/application-report.css') ?>?v=<?= substr(md5_file(FCPATH . 'assets/css/application-report.css'), 0, 8) ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/application-print.css') ?>?v=<?= substr(md5_file(FCPATH . 'assets/css/application-print.css'), 0, 8) ?>" rel="stylesheet">
</head>
<body>
<main class="report-shell">
    <nav class="toolbar" aria-label="Application print actions">
        <a class="toolbar-link" href="<?= site_url('administrator/' . (int) $application['id']) ?>">Back to application</a>
        <div class="toolbar-actions">
            <button class="toolbar-link" id="print-report" type="button">Print application</button>
            <a class="toolbar-button" href="<?= site_url('administrator/applications/' . (int) $application['id'] . '/pdf') ?>">Download PDF</a>
        </div>
    </nav>

    <article class="paper application-print-paper">
        <header class="letterhead">
            <img class="seal" src="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" alt="Santa Ignacia municipal seal">
            <div class="office">
                <p class="office-kicker">Republic of the Philippines - Province of Tarlac</p>
                <h1 class="office-name">Municipality of Santa Ignacia</h1>
                <p class="office-program">Municipal Scholarship Program</p>
            </div>
            <div class="document-mark">Application<br>Record</div>
        </header>

        <section class="report-title">
            <h2>Scholarship Application Record</h2>
            <p>Application reference #<?= (int) $application['id'] ?></p>
        </section>

        <section class="record-summary">
            <div><span>Applicant</span><strong><?= esc($application['applicant_name'] ?: 'Applicant') ?></strong></div>
            <div><span>Scholarship</span><strong><?= esc($application['scholarship_title']) ?></strong></div>
            <div><span>Provider</span><strong><?= esc($application['scholarship_provider'] ?? '') ?></strong></div>
            <div><span>Application status</span><strong><?= esc(ucwords(str_replace('_', ' ', $application['status']))) ?></strong></div>
            <div><span>Date submitted</span><strong><?= esc($application['created_at'] ?? '') ?></strong></div>
            <div><span>Contact</span><strong><?= esc($application['email'] ?? '') ?><br><?= esc($application['mobile_number'] ?? '') ?></strong></div>
        </section>

        <?php
        $printableDetails = array_filter(
            $details,
            static fn ($value, $key): bool => ! in_array($key, $skipDetails, true) && is_scalar($value) && (string) $value !== '',
            ARRAY_FILTER_USE_BOTH
        );
        ?>
        <?php if ($printableDetails !== []): ?>
            <section class="record-section">
                <h2>Applicant information</h2>
                <dl class="record-details">
                    <?php foreach ($printableDetails as $key => $value): ?>
                        <?php $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key)); ?>
                        <div><dt><?= esc($label) ?></dt><dd><?= nl2br(esc((string) $value)) ?></dd></div>
                    <?php endforeach ?>
                </dl>
            </section>
        <?php endif ?>

        <section class="record-section">
            <h2>Submitted documents <span><?= count($application['documents']) ?> file<?= count($application['documents']) === 1 ? '' : 's' ?></span></h2>
            <?php if ($application['documents'] === []): ?>
                <p class="record-empty">No documents were attached to this application.</p>
            <?php else: ?>
                <table class="document-table">
                    <thead><tr><th>Requirement</th><th>File name</th><th>Review status</th></tr></thead>
                    <tbody>
                        <?php foreach ($application['documents'] as $document): ?>
                            <tr>
                                <td><?= esc($document['requirement_name_snapshot']) ?></td>
                                <td><?= esc($document['original_filename']) ?></td>
                                <td><?= ! empty($document['admin_verified']) ? 'Verified' : 'Not yet verified' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </section>

        <?php if (! empty($application['remarks'])): ?>
            <section class="record-section">
                <h2>Administrator notes</h2>
                <p class="record-remarks"><?= nl2br(esc($application['remarks'])) ?></p>
            </section>
        <?php endif ?>

        <footer class="document-footer">
            <span class="footer-note">Municipal Scholarship System - Application #<?= (int) $application['id'] ?></span>
            <span>Printed <?= esc(date('F j, Y g:i A')) ?></span>
        </footer>
    </article>
</main>
<script src="<?= base_url('assets/js/application-report.js') ?>?v=<?= substr(md5_file(FCPATH . 'assets/js/application-report.js'), 0, 8) ?>"></script>
</body>
</html>
