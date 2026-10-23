<?php
$statusLabel = $filters['status'] !== '' ? ucwords(str_replace('_', ' ', $filters['status'])) : 'All statuses';
$generatedAt = date('F j, Y g:i A');
$pdfUrl = site_url('administrator/applications/report/pdf') . '?' . http_build_query($filters);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship Application Report | Municipal Scholarship</title>
    <link href="<?= base_url('assets/css/fonts.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/application-report.css') ?>?v=<?= substr(md5_file(FCPATH . 'assets/css/application-report.css'), 0, 8) ?>" rel="stylesheet">
</head>
<body>
<main class="report-shell">
    <nav class="toolbar" aria-label="Report actions">
        <a class="toolbar-link" href="<?= site_url('administrator/reports') ?>">Back to reports</a>
        <div class="toolbar-actions">
            <button class="toolbar-link" id="print-report" type="button">Print</button>
            <a class="toolbar-button" href="<?= esc($pdfUrl) ?>" download="scholarship-application-report.pdf">Download PDF</a>
        </div>
    </nav>

    <article class="paper" aria-label="Scholarship application report">
        <header class="letterhead">
            <img class="seal" src="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" alt="Santa Ignacia municipal seal">
            <div class="office">
                <p class="office-kicker">Republic of the Philippines - Province of Tarlac</p>
                <h2 class="office-name">Municipality of Santa Ignacia</h2>
                <p class="office-program">Municipal Scholarship Program</p>
            </div>
            <div class="document-mark">Administrative<br>Report</div>
        </header>

        <section class="report-title">
            <h1>Scholarship Application Report</h1>
            <p>Consolidated list of scholarship applications</p>
        </section>

        <section class="report-meta" aria-label="Report details">
            <div class="meta-item">
                <span class="meta-label">Report date</span>
                <span class="meta-value"><?= esc($generatedAt) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Application records</span>
                <span class="meta-value"><?= count($applications) ?> record<?= count($applications) === 1 ? '' : 's' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="meta-value"><?= esc($statusLabel) ?></span>
            </div>
        </section>

        <?php if ($filters['search'] !== '' || $scholarshipTitle !== ''): ?>
            <div class="filter-note" aria-label="Additional report filters">
                <?php if ($filters['search'] !== ''): ?><span><strong>Search:</strong> <?= esc($filters['search']) ?></span><?php endif ?>
                <?php if ($scholarshipTitle !== ''): ?><span><strong>Scholarship:</strong> <?= esc($scholarshipTitle) ?></span><?php endif ?>
            </div>
        <?php endif ?>

        <section aria-labelledby="records-heading">
            <div class="section-heading">
                <h2 id="records-heading">Application records</h2>
                <span>Sorted by submission date, newest first</span>
            </div>
            <?php if ($applications === []): ?>
                <div class="empty-state">No applications match the selected filters.</div>
            <?php else: ?>
                <div class="sheet-frame">
                <table class="records">
                    <colgroup>
                        <col class="row-number">
                        <col class="id">
                        <col class="applicant">
                        <col class="scholarship">
                        <col class="contact">
                        <col class="submitted">
                        <col class="status">
                    </colgroup>
                    <thead>
                        <tr class="column-letters" aria-hidden="true">
                            <th></th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th>
                        </tr>
                        <tr>
                            <th class="row-header" scope="col">#</th>
                            <th scope="col">Ref.</th>
                            <th scope="col">Applicant</th>
                            <th scope="col">Scholarship</th>
                            <th scope="col">Contact details</th>
                            <th scope="col">Date submitted</th>
                            <th scope="col">Application status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $rowIndex => $application): ?>
                            <?php
                            $status = (string) $application['status'];
                            $statusClass = in_array($status, ['approved', 'rejected', 'under_review', 'incomplete'], true) ? ' status-' . $status : '';
                            ?>
                            <tr>
                                <td class="row-index"><?= $rowIndex + 1 ?></td>
                                <td class="record-id">#<?= (int) $application['id'] ?></td>
                                <td><span class="applicant-name"><?= esc($application['applicant_name'] ?: 'Applicant') ?></span></td>
                                <td><?= esc($application['scholarship_title']) ?></td>
                                <td>
                                    <span class="contact-email"><?= esc($application['email'] ?? '') ?></span>
                                    <?php if (! empty($application['mobile_number'])): ?><span class="contact-phone"><?= esc($application['mobile_number']) ?></span><?php endif ?>
                                </td>
                                <td><?= esc($application['created_at'] ?? '') ?></td>
                                <td><span class="status<?= $statusClass ?>"><?= esc(ucwords(str_replace('_', ' ', $status))) ?></span></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                </div>
            <?php endif ?>
        </section>

        <footer class="document-footer">
            <span class="footer-note">Municipal Scholarship System - Administrative copy</span>
            <span>Generated <?= esc($generatedAt) ?></span>
        </footer>
    </article>
    <footer class="print-footer" aria-hidden="true">
        <span>Municipality of Santa Ignacia - Municipal Scholarship Program</span>
        <span>Official application report - <?= esc(date('F j, Y')) ?></span>
        <span>Page <span class="page-number"></span></span>
    </footer>
</main>
<script src="<?= base_url('assets/js/application-report.js') ?>?v=<?= substr(md5_file(FCPATH . 'assets/js/application-report.js'), 0, 8) ?>"></script>
</body>
</html>
