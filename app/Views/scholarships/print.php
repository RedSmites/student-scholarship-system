<?php
$isExpired = ! empty($scholarship['deadline']) && $scholarship['deadline'] < date('Y-m-d');
$status = $scholarship['status'] === 'open' && $isExpired ? 'Deadline passed' : ucwords((string) $scholarship['status']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($scholarship['title']) ?> | Scholarship record</title>
    <link href="<?= base_url('assets/css/fonts.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/application-report.css') ?>?v=<?= substr(md5_file(FCPATH . 'assets/css/application-report.css'), 0, 8) ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/application-print.css') ?>?v=<?= substr(md5_file(FCPATH . 'assets/css/application-print.css'), 0, 8) ?>" rel="stylesheet">
</head>
<body>
<main class="report-shell">
    <nav class="toolbar" aria-label="Scholarship print actions">
        <a class="toolbar-link" href="<?= site_url('scholarships') ?>">Back to scholarships</a>
        <div class="toolbar-actions">
            <button class="toolbar-link" id="print-report" type="button">Print scholarship</button>
        </div>
    </nav>

    <article class="paper application-print-paper scholarship-print-paper">
        <header class="letterhead">
            <img class="seal" src="<?= base_url('assets/img/sta_ignacia_logo.png') ?>" alt="Santa Ignacia municipal seal">
            <div class="office">
                <p class="office-kicker">Republic of the Philippines - Province of Tarlac</p>
                <h1 class="office-name">Municipality of Santa Ignacia</h1>
                <p class="office-program">Municipal Scholarship Program</p>
            </div>
            <div class="document-mark">Scholarship<br>Record</div>
        </header>

        <section class="report-title">
            <h2>Scholarship Program Record</h2>
            <p>Program reference #<?= (int) $scholarship['id'] ?></p>
        </section>

        <section class="record-summary">
            <div><span>Scholarship program</span><strong><?= esc($scholarship['title']) ?></strong></div>
            <div><span>Provider</span><strong><?= esc($scholarship['provider']) ?></strong></div>
            <div><span>Program status</span><strong><?= esc($status) ?></strong></div>
            <div><span>Application deadline</span><strong><?= ! empty($scholarship['deadline']) ? esc(date('M j, Y', strtotime($scholarship['deadline']))) : 'No deadline' ?></strong></div>
            <div><span>Date added</span><strong><?= ! empty($scholarship['created_at']) ? esc(date('M j, Y g:i A', strtotime($scholarship['created_at']))) : 'Not available' ?></strong></div>
            <div><span>Active requirements</span><strong><?= count($requirements) ?> document<?= count($requirements) === 1 ? '' : 's' ?></strong></div>
        </section>

        <?php foreach (['description' => 'Program description', 'eligibility' => 'Eligibility', 'benefits' => 'Benefits'] as $field => $label): ?>
            <?php if (trim((string) ($scholarship[$field] ?? '')) !== ''): ?>
                <section class="record-section">
                    <h2><?= esc($label) ?></h2>
                    <p class="record-remarks"><?= nl2br(esc($scholarship[$field])) ?></p>
                </section>
            <?php endif ?>
        <?php endforeach ?>

        <section class="record-section">
            <h2>Application requirements <span><?= count($requirements) ?> item<?= count($requirements) === 1 ? '' : 's' ?></span></h2>
            <?php if ($requirements === []): ?>
                <p class="record-empty">No active document requirements are configured for this scholarship.</p>
            <?php else: ?>
                <table class="document-table">
                    <thead><tr><th>Requirement</th><th>Instructions</th><th>Type</th></tr></thead>
                    <tbody>
                        <?php foreach ($requirements as $requirement): ?>
                            <tr>
                                <td><?= esc($requirement['requirement_name']) ?></td>
                                <td><?= trim((string) ($requirement['description'] ?? '')) !== '' ? nl2br(esc($requirement['description'])) : 'No additional instructions provided.' ?></td>
                                <td><?= ! empty($requirement['is_required']) ? 'Required' : 'Optional' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </section>

        <footer class="document-footer">
            <span class="footer-note">Municipal Scholarship System - Program #<?= (int) $scholarship['id'] ?></span>
            <span>Printed <?= esc(date('F j, Y g:i A')) ?></span>
        </footer>
    </article>
</main>
<script src="<?= base_url('assets/js/application-report.js') ?>?v=<?= substr(md5_file(FCPATH . 'assets/js/application-report.js'), 0, 8) ?>"></script>
</body>
</html>
