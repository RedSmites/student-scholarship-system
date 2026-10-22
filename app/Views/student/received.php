<?php $title = 'Application Received'; include __DIR__ . '/_header.php'; ?>
<section class="received-page"><div class="received-mark"><i class="bi bi-check-circle"></i></div><p class="text-uppercase small fw-bold text-primary">Application received</p><h1>Thank you, <?= esc($name) ?>.</h1><p>Your application has been submitted to the Municipal Scholarship Office. Keep your contact details available in case the office needs to follow up.</p><a class="btn-cta" href="<?= site_url('/') ?>">Back to home <span>→</span></a></section>
<?php include __DIR__ . '/_footer.php'; ?>
