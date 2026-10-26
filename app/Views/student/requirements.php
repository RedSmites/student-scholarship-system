<?php $title = 'Scholarships & Requirements'; include __DIR__ . '/_header.php'; ?>
<div class="page-title text-center" data-aos="fade"><div class="container"><h1 class="mb-2">Scholarships &amp; Requirements</h1></div></div>
<section class="section"><div class="container scholarship-requirements-list">
<?php if (empty($scholarships)): ?>
  <div class="empty-scholarships text-center"><i class="bi bi-info-circle"></i><h2>No scholarships are open right now</h2><p>Please check back later or contact the Municipal Scholarship Office for information.</p></div>
<?php else: foreach ($scholarships as $scholarship): ?>
  <article class="scholarship-requirements-card">
    <div class="scholarship-card-heading"><div><span class="scholarship-status"><i class="bi bi-circle-fill"></i> Open for applications</span><h2><?= esc(preg_replace('/^\s*\[DEMO\]\s*/i', '', $scholarship['title'])) ?></h2><p class="scholarship-provider"><?= esc($scholarship['provider']) ?></p></div><a class="btn-cta" href="<?= site_url('apply?scholarship_id=' . (int) $scholarship['id']) ?>">Apply for this scholarship</a></div>
    <?php if (! empty($scholarship['description'])): ?><p class="scholarship-description"><?= nl2br(esc(preg_replace('/^\s*(?:\[DEMO[^\]]*\]\s*)?(?:(?:Sample|Example)\b[^.]*\.\s*)?/i', '', $scholarship['description']))) ?></p><?php endif ?>
    <div class="scholarship-facts">
      <?php if (! empty($scholarship['eligibility'])): ?><div><h3><i class="bi bi-person-check"></i> Eligibility</h3><p><?= nl2br(esc(preg_replace('/^Proposed demonstration criteria:\s*/i', '', $scholarship['eligibility']))) ?></p></div><?php endif ?>
      <?php if (! empty($scholarship['benefits'])): ?><div><h3><i class="bi bi-award"></i> Benefits</h3><p><?= nl2br(esc(preg_replace('/^Demonstration benefit(?: only)?:\s*/i', '', preg_replace('/^Demonstration benefit only\.\s*/i', '', $scholarship['benefits'])))) ?></p></div><?php endif ?>
      <div><h3><i class="bi bi-calendar-event"></i> Application deadline</h3><p><?= ! empty($scholarship['deadline']) ? esc(date('F j, Y', strtotime($scholarship['deadline']))) : 'No deadline specified' ?></p></div>
    </div>
    <div class="scholarship-documents"><h3><i class="bi bi-file-earmark-text"></i> Required documents</h3>
      <?php if (empty($scholarship['requirements'])): ?><p class="no-requirements">The scholarship office has not added specific document requirements for this scholarship yet. Confirm any required documents with the office.</p>
      <?php else: ?><div class="requirement-rows"><?php foreach ($scholarship['requirements'] as $requirement): ?>
        <article class="requirement-row"><div class="requirement-content"><h4><?= esc($requirement['requirement_name']) ?><?= empty($requirement['is_required']) ? ' <span class="optional-label">Optional</span>' : '' ?></h4><?php if (! empty($requirement['description'])): ?><p><?= nl2br(esc($requirement['description'])) ?></p><?php endif ?></div></article>
      <?php endforeach ?></div><?php endif ?>
    </div>
  </article>
<?php endforeach; endif ?>
</div></section>
<?php include __DIR__ . '/_footer.php'; ?>
